<?php

namespace App\Http\Controllers;

use App\Models\Conference;
use App\Models\ConferenceSchedule;
use App\Models\ConferenceScheduleRating;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ConferenceScheduleRatingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ConferenceScheduleRating $conferenceScheduleRating)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ConferenceScheduleRating $conferenceScheduleRating)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ConferenceScheduleRating $conferenceScheduleRating)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ConferenceScheduleRating $conferenceScheduleRating)
    {
        //
    }

    /**
     * Show the application dashboard.
     * @param  \Illuminate\Http\Request  $request
     * @param \App\Models\ConferenceSchedule $conference_schedule
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function form(ConferenceSchedule $conference_schedule, Request $request)
    {
        $conference = Conference::find(session('selected_conference'));

        return view('rating.form')->with(['conference' => $conference, 'conference_schedule' => $conference_schedule, 'uuid' => $request->d]);
    }

    /**
     * Store a newly created resource in storage.
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function saveForm(Request $request)
    {
        $conference_schedule = ConferenceSchedule::find($request->conference_schedule_id);

        if(!$conference_schedule) {
            return view('rating.error');
        }

        $conference_schedule_rating = ConferenceScheduleRating::firstOrNew(['uuid' => $request->uuid, 'conference_schedule_id' => $request->conference_schedule_id]);
        $conference_schedule_rating->overall_rating = $request->overall_rating;
        $conference_schedule_rating->participant_ratings = !empty($request->participant_ratings) ? json_encode($request->participant_ratings) : NULL;
        $conference_schedule_rating->comments = $request->comments;
        if($conference_schedule_rating->save()) {
            $conference = Conference::find(session('selected_conference'));
            return view('rating.success')->with('conference', $conference);
        }

        return view('rating.error');

    }
}
