<?php

namespace App\Models;

use App\Http\Resources\PublicScheduleAnnouncementResource;
use App\Http\Resources\PublicScheduleEventParticipantResource;
use App\Http\Resources\PublicScheduleParticipantResource;
use App\Http\Resources\PublicScheduleResource;
use App\Http\Resources\PublicScheduleSponsorResource;
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

    public function session_interests()
    {
        return $this->hasMany( SessionInterest::class, 'conference_session_id', 'conference_session_id');
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
            $rooms = Room::where('conference_id', $conference_id)->orderBy('display_order')->orderBy('name')->get();
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
                        "track_color" =>  $session_info->track->color_code,
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
                'cs.description', 'cs.staff_notes', 'cs.ignore_errors', 'cs.attendance',
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

        $schedule_warnings = ScheduleWarnings::key_value_pairs();

        $schedule_list = [];
        $current_session = 0;
        $previous_session = 0;
        foreach($schedule_participants as $p) {

            // Checks specific to the same session
            if ($p->session_id == $current_session) {
                // Check for two time entries
                if (!in_array( "{$p->date} {$p->time}", $schedule_list[$current_session]['date_time'])) {
                    $schedule_list[$current_session]['date_time'][] = "{$p->date} {$p->time}";
                }
            }

            // Checks for new sessions
            if($p->session_id != $current_session) {
                $previous_session = $current_session;
                $current_session = $p->session_id;
                $schedule_list[$current_session] = [
                    'session_id'        => $p->session_id,
                    'session_name'      => $p->session_name,
                    'description'       => $p->description,
                    'staff_notes'       => $p->staff_notes,
                    'attendance'        => $p->attendance,
                    'ignore_errors'     => $p->ignore_errors,
                    'date_time'         => ["{$p->date} {$p->time}"],
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
                    if(!in_array($p->status_id, [4,5]) && !empty($schedule_warnings['check_status'])) {
                        $schedule_list[$current_session]['errors']['status'] = 'Session status not ready for scheduling';
                    }

                    if(!$p->user_id && !empty($schedule_warnings['zero_participants'])) {
                        $schedule_list[$current_session]['errors']['zero_participants'] = 'No participants assigned';
                    }

                    $previous_session_participants = !empty($schedule_list[$previous_session]['sublist']) ? count($schedule_list[$previous_session]['sublist']) : 0;

                    if($previous_session && $schedule_list[$previous_session]['session_type_id'] == '1' && !empty($schedule_warnings['min_panelists']) && $previous_session_participants < $schedule_warnings['min_panelists']) {
                        $schedule_list[$previous_session]['errors']['small_panel'] = "Less than {$schedule_warnings['min_panelists']} people on this panel";
                    }

                    if($previous_session && $schedule_list[$previous_session]['session_type_id'] == '1' && !empty($schedule_warnings['max_panelists']) && $previous_session_participants > $schedule_warnings['max_panelists']) {
                        $schedule_list[$previous_session]['errors']['large_panel'] = "More than {$schedule_warnings['max_panelists']} people on this session";
                    }

                    if($p->session_type_id == '1' && !$p->is_moderator && !empty($schedule_warnings['require_moderator'])) {
                        $schedule_list[$current_session]['errors']['no_moderator'] = 'No moderator on panel';
                    }
                }

            }



            if ($p->user_id) {
                // Don't redo entries if a session is in to different hours
                // TODO Above - turn schedule date into an array or something in case it's booked twice on the schedule
                if(empty($schedule_list[$current_session]['sublist'][$p->user_id])) {

                    $schedule_list[$current_session]['sublist'][$p->user_id] = [
                        'user_id' => $p->user_id,
                        'badge_name' => $p->badge_name,
                        'is_moderator' => $p->is_moderator,
                    ];
                }

            } elseif (empty($schedule_list[$current_session]['errors']['zero_participants'])) {
                $schedule_list[$current_session]['errors']['blank_participants'] = 'A blank participant entry exists';

            }


        }

        return $schedule_list;

    }

    public static function publicSchedule()
    {

        $default_conference_id = SiteConfig::where('key', 'default_conference_id')->first();
        $conference = Conference::where('id', $default_conference_id->value)->first();

        $schedule['conferenceName'] = $conference->short_name;

        $tracks = Track::select('id','name')->where('administrative', '0')->get();
        $schedule['tracks'] = $tracks;

        // TODO make rooms respect the conference_id
        $rooms = Room::select('id', 'name')->where('conference_id',$default_conference_id->value )->get();
        //$rooms = Room::select('id', 'name')->get();
        $schedule['locations'] = $rooms;

        $events = ConferenceSchedule::with('session')
                ->where('conference_id', $default_conference_id->value)
                ->whereHas('track', function($query) {
                    $query->where('administrative', 0);
                })
                ->get();
        $eventResource = PublicScheduleResource::collection($events);
        $schedule['events'] = $eventResource;


        $participants = User::whereHas('session_interest', function($query) use ($default_conference_id) {
                        $query->where('is_participant', 1);
                        $query->whereHas('conference_session', function($query) use($default_conference_id) {
                            $query->where('session_status_id', '5');
                            $query->where('conference_id', $default_conference_id->value);
                        });
                        $query->whereHas('conference_schedule', function($query) use($default_conference_id) {
                            $query->where('conference_id', $default_conference_id->value);
                        });
                    })
                    ->get();

        $schedule['presenters'] = PublicScheduleParticipantResource::collection($participants);

        $event_presenters =  DB::table('users as u')
            ->join('session_interests AS si', function($join) {
                $join->on( 'u.id', '=', 'si.user_id')
                    ->where('si.is_participant', '=', '1');
            })
            ->join('conference_schedules AS csh', 'si.conference_session_id', '=', 'csh.conference_session_id')
            ->select('u.id as user_id', 'csh.id AS event_id', 'si.is_moderator')
            ->where('csh.conference_id', '=',  $default_conference_id->value)
            ->orderBy('u.id')
            ->get();

        $schedule['eventPresenters'] = PublicScheduleEventParticipantResource::collection($event_presenters);

        $sponsors = ConferenceSponsor::orderBy('display_order', 'ASC')
            ->orderBy('name', 'ASC')
            ->where('conference_id', $default_conference_id->value)->get();
        $schedule['sponsors'] = PublicScheduleSponsorResource::collection($sponsors);

        return $schedule;
    }

    public static function publicScheduleTest()
    {

        $default_conference_id = SiteConfig::where('key', 'default_conference_id')->first();
        $conference = Conference::where('id', $default_conference_id->value)->first();

        $schedule['conferenceName'] = $conference->short_name;

        $tracks = Track::select('id','name')->where('administrative', '0')->get();
        $schedule['tracks'] = $tracks;

        // TODO make rooms respect the conference_id
        $rooms = Room::select('id', 'name')->where('conference_id',$default_conference_id->value )->get();
        //$rooms = Room::select('id', 'name')->get();
        $schedule['locations'] = $rooms;

        $events = ConferenceSchedule::with('session')
            ->where('conference_id', $default_conference_id->value)
            ->whereHas('track', function($query) {
                $query->where('administrative', 0);
            })
            ->get();
        $eventResource = PublicScheduleResource::collection($events);
        $schedule['events'] = $eventResource;


        $participants = User::whereHas('session_interest', function($query) use ($default_conference_id) {
            $query->where('is_participant', 1);
            $query->whereHas('conference_session', function($query) use($default_conference_id) {
                $query->where('session_status_id', '5');
                $query->where('conference_id', $default_conference_id->value);
            });
            $query->whereHas('conference_schedule', function($query) use($default_conference_id) {
                $query->where('conference_id', $default_conference_id->value);
            });
        })
            ->get();

        $schedule['presenters'] = PublicScheduleParticipantResource::collection($participants);

        $event_presenters =  DB::table('users as u')
            ->join('session_interests AS si', function($join) {
                $join->on( 'u.id', '=', 'si.user_id')
                    ->where('si.is_participant', '=', '1');
            })
            ->join('conference_schedules AS csh', 'si.conference_session_id', '=', 'csh.conference_session_id')
            ->select('u.id as user_id', 'csh.id AS event_id', 'si.is_moderator')
            ->where('csh.conference_id', '=',  $default_conference_id->value)
            ->orderBy('u.id')
            ->get();

        $schedule['eventPresenters'] = PublicScheduleEventParticipantResource::collection($event_presenters);

        $sponsors = ConferenceSponsor::orderBy('display_order', 'ASC')
            ->orderBy('name', 'ASC')
            ->where('conference_id', $default_conference_id->value)->get();
        $schedule['sponsors'] = PublicScheduleSponsorResource::collection($sponsors);

        $announcements = ConferenceAnnouncement::orderBy('pinned', 'DESC')
            ->orderBy('display_date', 'DESC')
            ->where('conference_id', $default_conference_id->value)->get();
        //TODO figure out the display_date time zone issues
        $schedule['announcements'] = PublicScheduleAnnouncementResource::collection($announcements);

        return $schedule;
    }


}
