<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConferenceSessionRequest;
use App\Http\Requests\PanelInterestRequest;
use App\Http\Requests\PresentationSessionRequest;
use App\Http\Requests\SessionIgnoreErrorsRequest;
use App\Http\Resources\CallForPanelistResource;
use App\Http\Resources\ConferenceSessionResource;
use App\Http\Resources\SessionInterestResource;
use App\Http\Resources\UserScheduledSessionInfoResource;
use App\Models\Conference;
use App\Models\ConferenceSchedule;
use App\Models\ConferenceSession;
use App\Models\SessionHistory;
use App\Models\SessionInterest;
use App\Models\SessionStatus;
use App\Models\SiteConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use mysql_xdevapi\Exception;

class ConferenceSessionController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $conference_sessions = ConferenceSession::orderBy('name')
            ->where('conference_id', session('selected_conference'))
            ->when($request->filled('keyword'), function ($query) use ($request) {
                $query->where('name', 'LIKE',"%$request->keyword%");
            })
            ->when($request->filled('track_id'), function ($query) use ($request) {
                if($request->track_id == 'empty'){
                    $query->whereNull('track_id');
                } else {
                    $query->where('track_id', $request->track_id);
                }
            })
            ->when($request->isNotFilled('all_statuses') && $request->isNotFilled('not_scheduled'), function ($query) {
                $query->whereNotIn('session_status_id', [6,7]);
            })
            ->when($request->filled('not_scheduled'), function ($query) {
                $query->whereNot('session_status_id', 5);
            })
            ->when($request->filled('type_id'), function ($query) use ($request) {
                $query->where('type_id', $request->type_id);
            });

        if($request->no_paginate) {
            return ConferenceSessionResource::collection($conference_sessions->get());
        }

        return ConferenceSessionResource::collection($conference_sessions->paginate(25));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\ConferenceSessionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ConferenceSessionRequest $request)
    {

        $conference_session = new ConferenceSession($request->all());
        $conference_session->special_equipment = $request->special_equipment ?? null;
        $conference_session->save();
        return new ConferenceSessionResource($conference_session);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ConferenceSession  $conference_session
     * @return \Illuminate\Http\Response
     */
    public function show(ConferenceSession $conference_session)
    {
        return new ConferenceSessionResource($conference_session);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\ConferenceSessionRequest  $request
     * @param  \App\Models\ConferenceSession  $conference_session
     * @return \Illuminate\Http\Response
     */
    public function update(ConferenceSessionRequest $request, ConferenceSession $conference_session)
    {
        //Save session title / desc changes if it is on the schedule
        if(ConferenceSchedule::where('conference_session_id', $conference_session->id)->first()) {
            $session_message = "Updated session id:  $conference_session->id. ";
            if($conference_session->name != $request->name) {
                $action_message = $session_message . "Changed title from: '$conference_session->name' to '$request->name'";
                SessionHistory::save_history(Auth::user()->id, $conference_session->id, 'updated_session_title', $action_message );
            }

            if($conference_session->description != $request->description) {
                $action_message = $session_message . "Description updated to: $request->description";
                SessionHistory::save_history(Auth::user()->id, $conference_session->id, 'updated_session_description', $action_message );
            }
        }

        $conference_session->name = $request->name;
        $conference_session->track_id = $request->track_id;
        $conference_session->type_id = $request->type_id;
        $conference_session->session_status_id = $request->session_status_id;
        $conference_session->description = $request->description;
        $conference_session->seed_questions = $request->seed_questions;
        $conference_session->staff_notes = $request->staff_notes;
        $conference_session->duration_minutes = $request->duration_minutes;
        $conference_session->special_equipment = $request->special_equipment ?? null;
        $conference_session->registration_required = $request->registration_required;
        $conference_session->max_registration = $request->max_registration;
        $conference_session->attendance = $request->attendance;
        $conference_session->save();
        return new ConferenceSessionResource($conference_session);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\SessionIgnoreErrorsRequest  $request
     * @param  \App\Models\ConferenceSession  $conference_session
     * @return \Illuminate\Http\Response
     */
    public function updateIgnoreErrors(SessionIgnoreErrorsRequest $request, ConferenceSession $conference_session)
    {

        if ($conference_session->update($request->only(['ignore_errors']))) {
            return response('success', 200);
        }
        abort(500, 'Cannot update ignore error status');
    }

    private function getConferenceIdFromSlug($slug)
    {
        $short_name = str_replace('-', ' ', $slug);
        $conference_id = Conference::where('short_name', $short_name)->value('id');

        if (empty($conference_id)) {
            abort(500, 'Conference not found');
        }

        return $conference_id;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  string  $conference_slug
     * @return \Illuminate\Http\Response
     */
    public function userPresentationIndex(string $conference_slug)
    {
        $conference_id = $this->getConferenceIdFromSlug($conference_slug);

        $user_id = Auth::user()->id;
        $conference_sessions = SessionInterest::getUserPresentationInterests($user_id, $conference_id);

        return SessionInterestResource::collection($conference_sessions);

    }

    /**
     * Display a listing of the resource.
     *
     * @param  string  $conference_slug
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function userPanelIndex(string $conference_slug, Request $request)
    {
        $conference_id = $this->getConferenceIdFromSlug($conference_slug);

        $user_id = Auth::user()->id;
        if($user_id) {
            $conference_sessions = ConferenceSession::getUserPanelInterests($user_id, $conference_id, $request);
            return CallForPanelistResource::collection($conference_sessions);
        }

        abort(403, 'Not authorized');

    }

    /**
     * Store a newly created presentation.
     *
     * @param  \App\Http\Requests\PresentationSessionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function storePresentation(PresentationSessionRequest $request)
    {
        $duration = SiteConfig::where('key', 'default_session_duration')->first();
        $status = SessionStatus::where('status', 'User Suggested')->first();


        $conference_session = new ConferenceSession($request->all());
        //Convert conference_id from slug
        $conference_session->conference_id = $this->getConferenceIdFromSlug($request->conference_id);
        $conference_session->type_id = config('site.session_type_id.presentation');
        $conference_session->session_status_id = $request->session_status_id ?? $status->id;
        $conference_session->special_equipment = $request->special_equipment ?? null;
        $conference_session->duration_minutes = $request->duration ?? $duration->value;
        $conference_session->save();

        $session_interest = new SessionInterest();
        $session_interest->conference_session_id = $conference_session->id;
        $session_interest->user_id = Auth::user()->id;
        $session_interest->is_participant = '1'; //Presentation submitters are participants by default
        $session_interest->save();

        return new ConferenceSessionResource($conference_session);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroyPresentation($id)
    {
        $user = Auth::user();
        $session_interest = SessionInterest::where('conference_session_id', $id)
                            ->where('user_id', $user->id)
                            ->whereHas('conference_session', function($query) {
                                $query->where('type_id', '=', config('site.session_type_id.presentation'));
                            })
                            ->first();

        if($session_interest) {
            $conference_session = ConferenceSession::find($id)->delete();
            if($conference_session) {
                return response('success', 200);
            }
        }

        abort(500, 'An error occurred deleting this presentation');

    }


    /**
     * Store a newly created panel interest record.
     *
     * @param  \App\Http\Requests\PanelInterestRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function storePanelInterest(PanelInterestRequest $request)
    {
        if(SessionInterest::where('conference_session_id', $request->conference_session_id)
                            ->where('user_id', $request->user_id)
                            ->first()
        ) {
            abort(400, 'A panel interest record already exists');
        }

        if(!$request->filled('id') && !$request->filled('user_id')) {
            $session_interest = new SessionInterest($request->all());
            $session_interest->user_id = Auth::user()->id;
            $session_interest->save();
            return new SessionInterestResource($session_interest);
        }

        abort(500, 'Cannot save this panel interest');

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\PanelInterestRequest  $request
     * @param  \App\Models\SessionInterest  $session_interest
     * @return \Illuminate\Http\Response
     */
    public function updatePanelInterest(PanelInterestRequest $request, SessionInterest $session_interest)
    {
        if($session_interest->user_id == Auth::user()->id && $request->conference_session_id == $session_interest->conference_session_id) {
            $session_interest->interest_level = $request->interest_level;
            $session_interest->experience_level = $request->experience_level;
            $session_interest->panel_role = $request->panel_role;
            $session_interest->notes = $request->notes;
            $session_interest->will_moderate = $request->will_moderate;
            $session_interest->save();
            return new SessionInterestResource($session_interest);
        }

        abort(403, 'Permission to update session interest denied.');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroyPanelInterest($id)
    {
        $user = Auth::user();
        $session_interest = SessionInterest::where('id', $id)
            ->where('user_id', $user->id)
            ->whereHas('conference_session', function($query) {
                $query->where('type_id', config('site.session_type_id.panel'));
                $query->where('session_status_id', 2);
            })
            ->first();

        if($session_interest) {
            $deleted_session = $session_interest->delete();
            if($deleted_session) {
                return response('success', 200);
            }
        }

        abort(500, 'An error occurred deleting this panel interest');

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $session_id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function userScheduledSessionInfo(Request $request, int $session_id)
    {

        //Get scheduled session info with active participants
        $session = ConferenceSession::where('id', $session_id)
                    ->where('session_status_id', 5)
                    ->with('conference', 'session_participants', 'track', 'session_type', 'conference_schedule')
                    ->first();

        $user_id = Auth::user()->id;
        //Check user is in session
        if($session->session_participants->contains('user_id', $user_id)) {
            return new UserScheduledSessionInfoResource($session);
        }

        abort(400, 'Not found');

    }

}
