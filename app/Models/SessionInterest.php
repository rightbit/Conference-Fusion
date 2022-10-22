<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

    public static function getParticipantListReport($conference_id, $request)
    {
        $query =  DB::table('session_interests AS si')
            ->join('user_infos AS ui', 'si.user_id', '=', 'ui.user_id')
            ->join('users AS u', 'si.user_id', '=', 'u.id')
            ->join('conference_sessions AS cs', 'si.conference_session_id', '=', 'cs.id')
            ->join('conference_schedules AS csh', 'si.conference_session_id', '=', 'csh.conference_session_id')
            ->join('rooms AS r', 'csh.room_id', '=', 'r.id')
            ->join('tracks AS t', 'cs.track_id', '=', 't.id')
            ->join('session_types AS st', 'cs.type_id', '=', 'st.id')
            ->select('u.id as user_id','u.last_name', 'u.first_name', 'u.email', 'ui.badge_name', 'ui.staff_notes',
                'ui.share_email_permission', 'ui.recording_permission', 'cs.id as session_id', 'cs.name as session_name', 'csh.date', 'csh.time',
                'csh.room_id', 'r.name as room_name','r.capacity', 'r.has_av', 'cs.track_id', 't.name as track_name',
                'si.is_moderator', 'si.staff_notes as session_user_staff_notes', 'st.id as session_type_id', 'st.name as session_type')
            ->where('si.is_participant', '=', '1')
            ->where('csh.conference_id', '=', $conference_id)
            ->orderBy('u.last_name')
            ->orderBy('u.id')
            ->orderBy('csh.date')
            ->orderBy('csh.time');

        if($request->type_id) {
            $query->where('cs.type_id', $request->type_id);
        }

        $schedule_participants = $query->get();


        $participant_list = [];
        $current_user = 0;
        $previous_user = 0;
        $previous_session_timestamp = 0;
        $two_sessions_in_a_row = false;
        foreach($schedule_participants as $p) {
            if($p->user_id != $current_user) {
                //Check for previous user errors
                if($previous_user && count($participant_list[$previous_user]['sessions']) < 3) {
                    $participant_list[$previous_user]['errors']['under_session_limit'] = 'User is in less than three sessions';
                }

                $two_sessions_in_a_row = false;
                $previous_user = $current_user;
                $current_user = $p->user_id;
                $previous_session_timestamp = 0;
                $participant_list[$current_user] = [
                    'user_id'                   => $p->user_id,
                    'first_name'                => $p->first_name,
                    'last_name'                 => $p->last_name,
                    'badge_name'                => $p->badge_name,
                    'email'                     => $p->email,
                    'share_email_permission'    => $p->share_email_permission,
                    'recording_permission'      => $p->recording_permission,
                    'staff_notes'               => $p->staff_notes,
                    'sessions'                  => [],
                    'errors'                    => null,
                ];
            }


            $participant_list[$current_user]['sessions'][$p->session_id] = [
                'session_id'            => $p->session_id,
                'session_name'          => $p->session_name,
                'session_user_staff_notes'   => $p->session_user_staff_notes,
                'is_moderator'          => $p->is_moderator,
                'date'                  => $p->date,
                'time'                  => $p->time,
                'room_name'             => $p->room_name,
                'capacity'              => $p->capacity,
                'has_av'                => $p->has_av,
                'track_id'              => $p->track_id,
                'track_name'            => $p->track_name,
                'session_type_id'       => $p->session_type_id,
                'session_type'          => $p->session_type,
            ];

            //Check for errors
            $current_session_timestamp = strtotime($p->date . " " . $p->time);
            if($current_session_timestamp === $previous_session_timestamp) {
                $participant_list[$current_user]['errors']['time_conflict'] = 'User is in two sessions at the same time';
            }

            if($current_session_timestamp - $previous_session_timestamp <= 3600) {
                if($two_sessions_in_a_row === true) {
                    $participant_list[$current_user]['errors']['busy_user'] = 'User is in several sessions in a row';
                }

                $two_sessions_in_a_row = true;

            } else {
                $two_sessions_in_a_row = false;

            }

            $previous_session_timestamp = $current_session_timestamp;

        }

        // Check last user errors
        if($previous_user && count($participant_list[$previous_user]['sessions']) < 3) {
            $participant_list[$previous_user]['errors']['under_session_limit'] = 'User is in less than three sessions';
        }

        return $participant_list;

    }


}
