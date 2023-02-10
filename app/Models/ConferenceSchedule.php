<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConferenceSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'conference_id',
        'conference_session_id',
        'track_id',
        'room_id',
        'date',
        'time',
    ];



    public function conference()
    {
        return $this->hasOne(Conference::class, 'id', 'conference_id');
    }


    public function session()
    {
        return $this->hasOne(ConferenceSession::class, 'id', 'conference_session_id')->withDefault([
            'id' => null,
            'name' => '',
        ]);
    }

    public function track()
    {
        return $this->hasOne(Track::class, 'id', 'track_id')->withDefault([
            'id' => null,
            'name' => '',
        ]);
    }
    public function room()
    {
        return $this->hasOne(Room::class, 'id', 'room_id')->withDefault([
            'id' => null,
            'name' => '',
        ]);
    }

    public static function getConferenceSchedule($conference_id, $date, $room_id = 0)
    {

        if ($room_id){
            $rooms = Room::find($room_id);
        } else {
            $rooms = Room::orderBy('display_order')->orderBy('name')->get();
        }

        $time_range = Conference::find($conference_id);
        $start_time = strtotime('1970-01-01 '.$time_range->session_start_time);
        $end_time = strtotime('1970-01-01 '.$time_range->session_end_time);

        $sessions = self::with('room')
            ->with('session')
            ->with('track')
            ->where('conference_id', $conference_id)
            ->where('date', $date)
            ->orderBy('time')
            ->get();

        $schedule_resource = [];
        $schedule_resource['rooms'] = [];
        foreach($rooms as $room) {
            $schedule_resource['rooms'][$room->name] = [
                "id" => $room->id,
                "name" => $room->name,
                "capacity" => $room->capacity,
                "has_av" => $room->has_av,
                "notes" => $room->notes,
            ];
        }

        $schedule_resource['timeslots'] = [];
        foreach(range($start_time, $end_time, 3600) as $time) {
            $time_stamp = date("H:00", $time);
            foreach($schedule_resource['rooms'] as $room) {
                $session_info =  $sessions->where('time', $time_stamp)->where('room_id', $room['id'])->first();
                if($session_info) {
                    $schedule_resource['timeslots'][$time_stamp][$room['name']] = [
                        "id" => $session_info->id,
                        "conference_id" => (int)$conference_id,
                        "session_id" => $session_info->session->id,
                        "session_name" => $session_info->session->name,
                        "track_id" =>  $session_info->track->id,
                        "track_name" =>  $session_info->track->name,
                        "room_id" =>  $session_info->room->id,
                        "date" =>  $session_info->date,
                        "time" =>  $session_info->time,
                    ];
                } else {
                    $schedule_resource['timeslots'][$time_stamp][$room['name']] = [];
                }
            }


        }

        return $schedule_resource;

    }

    public static function getConferenceScheduleListReport($conference_id, $request)
    {
        $query =  DB::table('conference_schedules AS csh')
            ->join('conference_sessions AS cs', 'csh.conference_session_id', '=', 'cs.id')
            ->leftJoin('session_interests AS si', function($join) {
                $join->on( 'csh.conference_session_id', '=', 'si.conference_session_id')
                    ->where('si.is_participant', '=', '1');
            })
            ->leftJoin('user_infos AS ui', 'si.user_id', '=', 'ui.user_id')
            ->join('rooms AS r', 'csh.room_id', '=', 'r.id')
            ->join('tracks AS t', 'cs.track_id', '=', 't.id')
            ->join('session_types AS st', 'cs.type_id', '=', 'st.id')
            ->join('session_statuses AS ss', 'cs.session_status_id', '=', 'ss.id')
            ->select('cs.id AS session_id', 'cs.name AS session_name', 'ss.id AS status_id',
                'cs.description', 'cs.staff_notes',
                'ss.status AS status_name', 'csh.date', 'csh.time',
                'ui.user_id', 'ui.badge_name', 'si.is_moderator', 'si.interest_level', 'si.experience_level',
                'csh.room_id', 'r.name as room_name', 'r.capacity', 'r.has_av', 'cs.track_id', 't.name AS track_name',
                'st.id AS session_type_id', 'st.name AS session_type')
            ->where('csh.conference_id', '=', $conference_id)
            ->orderBy('cs.name')
            ->orderBy('si.is_moderator', 'desc');

        if($request->track_id) {
            $query->where('cs.track_id', $request->track_id);
        }

        $schedule_participants = $query->get();


        $schedule_list = [];
        $current_session = 0;
        $previous_session = 0;
        foreach($schedule_participants as $p) {
            if($p->session_id != $current_session) {
                $previous_session = $current_session;
                $current_session = $p->session_id;
                $schedule_list[$current_session] = [
                    'session_id'        => $p->session_id,
                    'session_name'      => $p->session_name,
                    'description'       => $p->description,
                    'staff_notes'       => $p->staff_notes,
                    'date'              => $p->date,
                    'time'              => $p->time,
                    'room_name'         => $p->room_name,
                    'capacity'          => $p->capacity,
                    'has_av'            => $p->has_av,
                    'track_id'          => $p->track_id,
                    'track_name'        => $p->track_name,
                    'session_type_id'   => $p->session_type_id,
                    'session_type'      => $p->session_type,
                    'status_id'         => $p->status_id,
                    'status_name'       => $p->status_name,
                    'sublist'           => [],
                    'errors'            => null,
                ];

                if(!$request->skip_checks) {
                    //Check for problems
                    if(!in_array($p->status_id, [4,5])) {
                        $schedule_list[$current_session]['errors']['status'] = 'Session status not ready for scheduling';
                    }

                    if(!$p->user_id) {
                        $schedule_list[$current_session]['errors']['zero_participants'] = 'No participants assigned';
                    }

                    $previous_session_participants = !empty($schedule_list[$previous_session]['sublist']) ? count($schedule_list[$previous_session]['sublist']) : 0;

                    if($previous_session && $schedule_list[$previous_session]['session_type_id'] == '1' && in_array($previous_session_participants, [1,2])) {
                        $schedule_list[$previous_session]['errors']['small_panel'] = 'Less than three people on this panel';
                    }

                    if($previous_session && $schedule_list[$previous_session]['session_type_id'] == '1' && $previous_session_participants > 6) {
                        $schedule_list[$previous_session]['errors']['large_panel'] = 'More than six people on this session';
                    }

                    if($p->session_type_id == '1' && !$p->is_moderator) {
                        $schedule_list[$current_session]['errors']['no_moderator'] = 'No moderator on panel';
                    }
                }

            }

            if(!$request->skip_checks) {
                if ($p->user_id) {
                    if(!empty($schedule_list[$current_session]['sublist'][$p->user_id])) {
                        $schedule_list[$current_session]['errors']['duplicate_participants'] = "Duplicate entry for user {$p->user_id}";
                    }

                    $schedule_list[$current_session]['sublist'][$p->user_id] = [
                        'user_id'       => $p->user_id,
                        'badge_name'    => $p->badge_name,
                        'is_moderator'  => $p->is_moderator,
                    ];

                } elseif (empty($schedule_list[$current_session]['errors']['zero_participants'])) {
                    $schedule_list[$current_session]['errors']['blank_participants'] = 'A blank participant entry exists';

                }
            }

        }

        return $schedule_list;

    }

}
