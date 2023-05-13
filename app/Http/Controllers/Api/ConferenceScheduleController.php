<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConferenceScheduleRequest;
use App\Http\Resources\ConferenceScheduleResource;
use App\Models\ConferenceSchedule;
use App\Models\ConferenceSession;
use App\Models\Room;
use App\Models\SessionHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ConferenceScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $schedule =  ConferenceSchedule::getConferenceSchedule($request->conference_id, $request->date, $request->room_id);
        return new ConferenceScheduleResource($schedule);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\ConferenceScheduleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ConferenceScheduleRequest $request)
    {

        $existing_session = ConferenceSchedule::where('conference_id', $request->conference_id)
            ->where('room_id', $request->room_id)
            ->where('date', $request->date)
            ->where('time', $request->time)
            ->first();

        if($existing_session) {
            abort(422, "Session already exists");
        }

        $schedule = new ConferenceSchedule($request->all());
        //Can't save 0's because of foreign keys
        $schedule->conference_session_id ?: null;
        $schedule->track_id ?: null;
        $schedule->save();

        $conference_session = $request->conference_session_id ? ConferenceSession::find($request->conference_session_id) : null;
        $room = Room::find($request->room_id);
        if($conference_session) {
            $history_message = "Added session {$conference_session->name} to schedule at {$request->date} {$request->time} in room {$room->name}";
            SessionHistory::save_history(Auth::user()->id, $request->conference_session_id, 'added_to_schedule',  $history_message);
        }


        return new ConferenceScheduleResource($schedule);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ConferenceSchedule  $conferenceSchedule
     * @return \Illuminate\Http\Response
     */
    public function show(ConferenceSchedule $conferenceSchedule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ConferenceSchedule  $conferenceSchedule
     * @return \Illuminate\Http\Response
     */
    public function edit(ConferenceSchedule $conferenceSchedule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\ConferenceScheduleRequest  $request
     * @param  \App\Models\ConferenceSchedule  $conferenceSchedule
     * @return \Illuminate\Http\Response
     */
    public function update(ConferenceScheduleRequest $request, ConferenceSchedule $conferenceSchedule)
    {
        $old_conference_session = $conferenceSchedule->session->name ?? "No session";
        $conference_session_name = $request->conference_session_id ? ConferenceSession::where('id', $request->conference_session_id)->value('name') : "No session";
        $room = Room::find($request->room_id);
        $history_message = "Updated schedule, changed '{$old_conference_session}' to '{$conference_session_name}' to schedule at {$request->date} {$request->time} in room {$room->name}";
        $conference_session_id = $request->conference_session_id ?? $conferenceSchedule->conference_session_id;
        if($conference_session_id) {
            SessionHistory::save_history(Auth::user()->id, $conference_session_id, 'updated_schedule',  $history_message);
        }

        $conferenceSchedule->conference_session_id = $request->conference_session_id ?: null;
        $conferenceSchedule->track_id = $request->track_id ?: null;
        $conferenceSchedule->room_id = $request->room_id;
        $conferenceSchedule->date = $request->date;
        $conferenceSchedule->time = $request->time;
        $conferenceSchedule->save();

        return new ConferenceScheduleResource($conferenceSchedule);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ConferenceSchedule  $conferenceSchedule
     * @return \Illuminate\Http\Response
     */
    public function destroy(ConferenceSchedule $conferenceSchedule)
    {
        //
    }
}
