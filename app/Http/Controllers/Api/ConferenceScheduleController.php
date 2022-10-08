<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConferenceScheduleRequest;
use App\Http\Resources\ConferenceScheduleResource;
use App\Models\ConferenceSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ConferenceScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Http\Requests\ConferenceScheduleRequest $request
     * @return \Illuminate\Http\Response
     */
    public function index(ConferenceScheduleRequest $request)
    {
        $schedule =  ConferenceSchedule::getConferenceSchedule($request->conference_id, $request->date, $request->room_id);
        $hello = new ConferenceScheduleResource($schedule);

        Log::debug([$hello]);
        return $hello;
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ConferenceSchedule  $conferenceSchedule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ConferenceSchedule $conferenceSchedule)
    {
        //
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
