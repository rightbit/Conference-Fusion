<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PublicScheduleResource;
use App\Models\Conference;
use App\Models\ConferenceSchedule;
use App\Models\PublicUsersSchedule;
use App\Models\SiteConfig;
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     * @param  string  $uuid
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeSchedule(Request $request, string $uuid)
    {
        $default_conference_id = SiteConfig::where('key', 'default_conference_id')->first();
        $public_user_schedule = PublicUsersSchedule::firstOrCreate(['uuid' => $uuid, 'conference_id' => $default_conference_id->value]);
        $public_user_schedule->conference_id = $default_conference_id->value;
        $public_user_schedule->schedule = json_encode($request->all());
        $public_user_schedule->save();
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
