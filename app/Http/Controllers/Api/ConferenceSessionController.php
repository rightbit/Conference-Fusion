<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConferenceSessionRequest;
use App\Http\Requests\PanelInterestRequest;
use App\Http\Requests\PresentationSessionRequest;
use App\Http\Resources\CallForPanelistResource;
use App\Http\Resources\ConferenceSessionResource;
use App\Http\Resources\SessionInterestResource;
use App\Http\Resources\UserPresentationResource;
use App\Models\ConferenceSession;
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
            ->when($request->filled('keyword'), function ($query) use ($request) {
                $query->where('name', 'LIKE',"%$request->keyword%");
            })
            ->when($request->filled('track_id'), function ($query) use ($request) {
                $query->where('track_id', $request->track_id);
            })
            ->when($request->filled('type_id'), function ($query) use ($request) {
                $query->where('type_id', $request->type_id);
            });

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
        $conference_session->name = $request->name;
        $conference_session->track_id = $request->track_id;
        $conference_session->type_id = $request->type_id;
        $conference_session->session_status_id = $request->session_status_id;
        $conference_session->description = $request->description;
        $conference_session->seed_questions = $request->seed_questions;
        $conference_session->staff_notes = $request->staff_notes;
        $conference_session->duration_minutes = $request->duration_minutes;
        $conference_session->registration_required = $request->registration_required;
        $conference_session->max_registration = $request->max_registration;
        $conference_session->attendance = $request->attendance;
        $conference_session->save();
        return new ConferenceSessionResource($conference_session);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  int  $conference_id
     * @return \Illuminate\Http\Response
     */
    public function userPresentationIndex(int $conference_id)
    {
        $user_id = Auth::user()->id;
        $conference_sessions = SessionInterest::getUserPresentationInterests($user_id, $conference_id);

        return SessionInterestResource::collection($conference_sessions);
    }


    /**
     * Display a listing of the resource.
     *
     * @param  int  $conference_id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function userPanelIndex(int $conference_id, Request $request)
    {
        $user_id = Auth::user()->id;
        $conference_sessions = ConferenceSession::getUserPanelInterests($user_id, $conference_id, $request);

        return CallForPanelistResource::collection($conference_sessions);
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
        $conference_session->type_id = config('site.session_type_id.presentation');
        $conference_session->session_status_id = $request->session_status_id ?? $status->id;
        $conference_session->duration_minutes = $request->duration ?? $duration->value;
        $conference_session->save();

        $session_interest = new SessionInterest();
        $session_interest->conference_session_id = $conference_session->id;
        $session_interest->user_id = Auth::user()->id;
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

}
