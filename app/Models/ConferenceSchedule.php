<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class ConferenceSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'conference_id',
        'conference_session_id',
        'track_id',
        'date',
        'time',
    ];



    public function conference() {
        return $this->hasOne(Conference::class, 'id', 'conference_id');
    }


    public function session() {
        return $this->hasOne(ConferenceSession::class, 'id', 'conference_session_id')->withDefault([
            'id' => null,
            'name' => '',
        ]);
    }

    public function track() {
        return $this->hasOne(Track::class, 'id', 'track_id')->withDefault([
            'id' => null,
            'name' => '',
        ]);
    }
    public function room() {
        return $this->hasOne(Room::class, 'id', 'room_id')->withDefault([
            'id' => null,
            'name' => '',
        ]);
    }

    public static function getConferenceSchedule($conference_id, $date, $room_id = 0) {

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
            $schedule_resource['rooms'][] = [
                "id" => $room->id,
                "name" => $room->name,
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

        Log::debug($schedule_resource);
        return $schedule_resource;

    }

}
