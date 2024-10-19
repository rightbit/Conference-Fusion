<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use mysql_xdevapi\Collection;

class SessionInterest extends Model
{
    use HasFactory;

    protected $fillable = [
        'conference_session_id',
        'user_id',
        'interest_level',
        'experience_level',
        'panel_role',
        'notes',
        'staff_notes',
        'staff_score',
        'will_moderate',
        'is_participant',
        'is_moderator',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function user_info()
    {
        return $this->hasOne(UserInfo::class, 'user_id', 'user_id');
    }

    public function conference_session()
    {
        return $this->belongsTo(ConferenceSession::class);
    }

    public function conference_schedule()
    {
        return $this->hasMany(ConferenceSchedule::class, 'conference_session_id', 'conference_session_id');
    }

    public static function getUserPresentationInterests($user_id, $conference_id)
    {
        return self::where('user_id', $user_id)
                    ->whereHas('conference_session', function($query) use($conference_id) {
                        $query->where('conference_id', '=', $conference_id);
                    })
                    ->whereHas('conference_session', function($query) {
                        $query->where('type_id', '=', config('site.session_type_id.presentation'));
                    })
                    ->with('user','conference_session')
                    ->get();
    }

    public static function getUserSchedule(int $user_id, int $conference_id) {
        return  DB::table('session_interests AS si')
            ->select( 'cs.id as session_id', 'cs.name as session_name', 'csh.date', 'csh.time',
                 't.name as track_name',
                'si.is_moderator',  'st.name as session_type')
            ->join('conference_sessions AS cs', 'si.conference_session_id', '=', 'cs.id')
            ->join('conference_schedules AS csh', 'si.conference_session_id', '=', 'csh.conference_session_id')
            ->join('tracks AS t', 'cs.track_id', '=', 't.id')
            ->join('session_types AS st', 'cs.type_id', '=', 'st.id')
            ->where('si.user_id', "=", $user_id)
            ->where('si.is_participant', "=", 1)
            ->where('cs.session_status_id', "=", "5")
            ->where('csh.conference_id', '=', $conference_id)
            ->orderBy('csh.date')
            ->orderBy('csh.time')
            ->get();
    }

    public static function getParticipantListReport(int $conference_id, $request): ?array
    {
        $query =  DB::table('session_interests AS si')
            ->join('user_infos AS ui', 'si.user_id', '=', 'ui.user_id')
            ->join('users AS u', 'si.user_id', '=', 'u.id')
            ->join('conference_sessions AS cs', 'si.conference_session_id', '=', 'cs.id')
            ->join('conference_schedules AS csh', 'si.conference_session_id', '=', 'csh.conference_session_id')
            ->join('rooms AS r', 'csh.room_id', '=', 'r.id')
            ->join('tracks AS t', 'cs.track_id', '=', 't.id')
            ->join('session_types AS st', 'cs.type_id', '=', 'st.id')
            ->select('u.id as user_id','u.last_name', 'u.first_name', 'u.email', 'ui.contact_email',
                'ui.badge_name', 'ui.biography', 'ui.website', 'ui.social_data', 'ui.staff_notes',
                'ui.share_email_permission', 'cs.id as session_id', 'cs.name as session_name', 'csh.date', 'csh.time',
                'csh.room_id', 'r.name as room_name','r.capacity', 'r.has_av', 'cs.track_id', 't.name as track_name',
                'si.is_moderator', 'si.staff_notes as session_user_staff_notes', 'st.id as session_type_id', 'st.name as session_type')
            ->where('si.is_participant', '=', '1')
            ->where('csh.conference_id', '=', $conference_id)
            ->orderBy('u.last_name')
            ->orderBy('u.id')
            ->orderBy('csh.date')
            ->orderBy('csh.time');

//        if($request->user_id) {
//            $query->where('u.id', $request->user_id);
//        }

        $schedule_participants = $query->get();
        $schedule_warnings = ScheduleWarnings::key_value_pairs();

        $participant_list = [];
        $current_user = 0;
        $previous_user = 0;
        $previous_session_timestamp = 0;
        $sessions_in_a_row = 0;
        foreach($schedule_participants as $p) {
            if($p->user_id != $current_user) {
                //Check for previous user errors
                if(!empty($schedule_warnings['min_sessions'])) {
                    if($previous_user && count($participant_list[$previous_user]['sessions']) < $schedule_warnings['min_sessions']) {
                        $participant_list[$previous_user]['errors']['under_session_limit'] = "User is in less than {$schedule_warnings['min_sessions']} sessions";
                    }
                }


                $sessions_in_a_row = 0;
                $previous_user = $current_user;
                $current_user = $p->user_id;
                $previous_session_timestamp = 0;
                $participant_list[$current_user] = [
                    'user_id'                   => $p->user_id,
                    'first_name'                => $p->first_name,
                    'last_name'                 => $p->last_name,
                    'badge_name'                => $p->badge_name,
                    'biography'                 => $p->biography,
                    'website'                   => $p->website,
                    'social_data'               => $p->social_data,
                    'email'                     => $p->share_email_permission || $request->export_emails ? ($p->contact_email ?: $p->email) : null,
                    'share_email_permission'    => $p->share_email_permission,
                    'staff_notes'               => $p->staff_notes,
                    'sessions'                  => [],
                    'errors'                    => null,
                ];
            }

            if(empty($participant_list[$current_user]['sessions'][$p->session_id])) {
                $participant_list[$current_user]['sessions'][$p->session_id] = [
                    'session_id' => $p->session_id,
                    'session_name' => $p->session_name,
                    'session_user_staff_notes' => $p->session_user_staff_notes,
                    'is_moderator' => $p->is_moderator,
                    'date' => $p->date,
                    'time' => $p->time,
                    'room_name' => $p->room_name,
                    'capacity' => $p->capacity,
                    'has_av' => $p->has_av,
                    'track_id' => $p->track_id,
                    'track_name' => $p->track_name,
                    'session_type_id' => $p->session_type_id,
                    'session_type' => $p->session_type,
                    'multiple_hours' => 0,
                ];
            } else {
                $participant_list[$current_user]['sessions'][$p->session_id]['multiple_hours']++;
            }

            //Check for errors
            $current_session_timestamp = strtotime($p->date . " " . $p->time);
            if($current_session_timestamp === $previous_session_timestamp && !empty($schedule_warnings['same_time_sessions'])) {
                $participant_list[$current_user]['errors']['time_conflict'] = 'User is in two sessions at the same time';
            }

            if($current_session_timestamp - $previous_session_timestamp <= 3600 && !empty($schedule_warnings['max_consecutive_sessions'])) {
                if($sessions_in_a_row >= $schedule_warnings['max_consecutive_sessions']) {
                    $participant_list[$current_user]['errors']['busy_user'] = 'User is in several sessions in a row';
                }

                $sessions_in_a_row++;

            } else {
                $sessions_in_a_row = 0;

            }

            $previous_session_timestamp = $current_session_timestamp;

        }

        // Check last user errors
        if(!empty($schedule_warnings['min_sessions'])) {
            if($previous_user && count($participant_list[$previous_user]['sessions']) < $schedule_warnings['min_sessions']) {
                $participant_list[$previous_user]['errors']['under_session_limit'] = "User is in less than {$schedule_warnings['min_sessions']} sessions";
            }
        }

        return $participant_list;

    }

    public static function getPotentialsListReport(int $conference_id, $type_id = 1, $sort = 'last_name', $desc = false)
    {

        $desc = filter_var($desc, FILTER_VALIDATE_BOOLEAN) ? 'DESC' : 'ASC';

        return  DB::table('session_interests AS si')
            ->select(['si.user_id', 'u.first_name', 'u.last_name', 'ui.badge_name', DB::raw('COUNT(si.user_id) as total')])
            ->join('users AS u', 'si.user_id', '=', 'u.id')
            ->join('user_infos AS ui', 'si.user_id', '=', 'ui.user_id')
            ->join('conference_sessions AS cs', 'si.conference_session_id', '=', 'cs.id')
            ->where('cs.conference_id', "=", $conference_id)
            ->where('cs.type_id', "=", $type_id)
            ->groupBy('si.user_id', 'u.last_name', 'u.first_name', 'u.last_name', 'ui.badge_name')
            ->orderBy($sort, $desc)
            ->get();

    }

    public static function getNonParticipantListReport(int $conference_id): ?array
    {
        $non_participants = DB::select('
                                SELECT DISTINCT si.user_id, u.first_name, u.last_name, ui.badge_name, COALESCE(ui.contact_email, u.email) as email
                                FROM session_interests si
                                JOIN users u ON si.user_id = u.id
                                JOIN user_infos ui ON si.user_id = ui.user_id
                                JOIN conference_sessions cs on si.conference_session_id = cs.id
                                WHERE si.user_id NOT IN
                                    (SELECT DISTINCT si2.user_id
                                        FROM session_interests si2
                                        JOIN conference_schedules csh
                                        ON si2.conference_session_id = csh.conference_session_id
                                        AND si2.is_participant = 1
                                        AND csh.conference_id = :conference_id)
                                AND cs.conference_id = :conference_id2
                                AND si.notes NOT LIKE "Added by%"
                                ', ['conference_id' => $conference_id, 'conference_id2' => $conference_id]);

        return $non_participants;

    }


}
