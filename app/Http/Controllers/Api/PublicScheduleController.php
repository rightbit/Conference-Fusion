<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PublicUserScheduleRequest;
use App\Models\ConferenceSchedule;
use App\Models\PublicUsersSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PublicScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $schedule = ConferenceSchedule::publicSchedule();
        return $schedule;
    }

    /**
     * Display a listing of the resource.
     */
    public function indexTest()
    {

        $schedule = ConferenceSchedule::publicScheduleTest();
        return $schedule;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     * @param  string  $uuid
     * @param  \App\Http\Requests\PublicUserScheduleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function storeSchedule(PublicUserScheduleRequest $request, string $uuid)
    {
        //Log::debug($request);
        // Create an entry if the user has starred an event in the app. If the event has become unstarred, delete
        if ($request->get('starred')) {
            $public_user_schedule = PublicUsersSchedule::firstOrCreate(['uuid' => $uuid, 'conference_schedule_id' => $request->event_id]);
        } else {
            $public_user_schedule = PublicUsersSchedule::where(['uuid' => $uuid, 'conference_schedule_id' => $request->event_id])->delete();
        }

        return response('success', 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
