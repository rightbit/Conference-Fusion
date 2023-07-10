<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConferenceRequest;
use App\Http\Resources\ConferenceResource;
use App\Models\Conference;
use App\Models\ConferenceSchedule;
use App\Models\ConferenceSession;
use Illuminate\Http\Request;

class ConferenceController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->keyword){
            return ConferenceResource::collection(
                Conference::where('name', 'LIKE',"%$request->keyword%")
                    ->orWhere('start_date', 'LIKE',"%$request->keyword%")
                    ->orderby('start_date', 'DESC')->paginate(25)
            );
        }
        return ConferenceResource::collection(Conference::orderby('start_date', 'DESC')->paginate(25));
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
     * @param  \App\Http\Requests\ConferenceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ConferenceRequest $request)
    {
        $conference = new Conference($request->all());
        $conference->save();
        return new ConferenceResource($conference);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Conference  $conference
     * @return \Illuminate\Http\Response
     */
    public function show(Conference $conference)
    {
        return new ConferenceResource($conference);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Conference  $conference
     * @return \Illuminate\Http\Response
     */
    public function edit(Conference $conference)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\ConferenceRequest  $request
     * @param  \App\Models\Conference  $conference
     * @return \Illuminate\Http\Response
     */
    public function update(ConferenceRequest $request, Conference $conference)
    {
        if($request->id == $conference->id) {
            $conference->name = $request->name;
            $conference->short_name  = $request->short_name;
            $conference->description = $request->description;
            $conference->location = $request->location;
            $conference->url = $request->url;
            $conference->time_zone = $request->time_zone;
            $conference->start_date = $request->start_date;
            $conference->end_date = $request->end_date;
            $conference->session_start_time  = $request->session_start_time ;
            $conference->session_end_time = $request->session_end_time;
            $conference->call_start_date = $request->call_start_date;
            $conference->call_end_date = $request->call_end_date;
            $conference->saveOrFail();
            return new ConferenceResource($conference);
        } else {
            abort(422, 'Could not validate the request');

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Conference  $conference
     * @return \Illuminate\Http\Response
     */
    public function destroy(Conference $conference)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  int  $conference_id
     * @return \Illuminate\Http\Response
     */
    public function proposedTimeSync($conference_id)
    {
        $sessions = ConferenceSession::where('conference_id', '=', $conference_id)->get();
        foreach ($sessions as $session) {
            $schedule = ConferenceSchedule::where('conference_session_id', '=', $session->id)
                        ->where('conference_id', '=', $conference_id)
                        ->first();
            if($schedule) {
                $session->proposed_date = $schedule->date . ' ' . $schedule->time;
                $session->save();
            }
        }
        return response()->noContent();
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  int  $conference_id
     * @return \Illuminate\Http\Response
     */
    public function scheduledStatusSync($conference_id)
    {
        $sessions = ConferenceSession::where('conference_id', '=', $conference_id)->get();
        foreach ($sessions as $session) {
            $schedule = ConferenceSchedule::where('conference_session_id', '=', $session->id)
                ->where('conference_id', '=', $conference_id)
                ->first();
            if ($schedule) {
                $session->session_status_id = 5;
                $session->save();
            } else {
                if ($session->session_status_id == 5) {
                    $session->session_status_id = 3;
                    $session->save();
                }
            }
        }
        return response()->noContent();
    }
}
