<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SessionInterestResource;
use App\Models\SessionHistory;
use App\Models\SessionInterest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class SessionInterestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!Gate::allows('view_admin', Auth::user())){
            abort(403, 'Not authorized');
        }

        $session_interest_users = SessionInterest::
            when($request->filled('session_id'), function ($query) use ($request) {
                $query->where('conference_session_id', $request->session_id);
            })
            ->when($request->filled('partipants_only'), function ($query) use ($request) {
                $query->where('is_participant', '1');
            })
            ->when($request->filled('non_partipants'), function ($query) use ($request) {
                $query->where('is_participant', '0');
            })
            ->when($request->filled('keyword'), function ($query) use ($request) {
                $query->whereHas('user', function($query) use($request) {
                    $query->where('first_name', 'LIKE',"%$request->keyword%");
                })
                ->orWhereHas('user', function($query) use($request) {
                    $query->where('last_name', 'LIKE',"%$request->keyword%");
                })
                ->orWhereHas('user_info', function($query) use($request) {
                    $query->where('badge_name', 'LIKE',"%$request->keyword%");
                });
            })
            ->orderBy('staff_score', 'desc')
            ->orderBy('interest_level', 'desc');

        return SessionInterestResource::collection($session_interest_users->paginate(50));

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
        $session_interest_exists = SessionInterest::where('user_id', $request->user_id)
                                                    ->where('conference_session_id', $request->conference_session_id)
                                                    ->first();

        if($session_interest_exists) {
            abort(409, "Participant interest record already exists");
        }

        $session_interest = new SessionInterest($request->all());
        if(!$session_interest->notes) {
            $session_interest->notes = 'Added by: ' . Auth::user()->first_name . ' ' . Auth::user()->last_name;
        }
        $session_interest->save();

        $history_message =  "Added user: {$session_interest->user_info->badge_name} to {$session_interest->conference_session->name}";
        SessionHistory::save_history(Auth::user()->id, $session_interest->conference_session_id, 'updated_session_participants',  $history_message);

        return new SessionInterestResource($session_interest);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SessionInterest  $sessionInterest
     * @return \Illuminate\Http\Response
     */
    public function show(SessionInterest $sessionInterest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SessionInterest  $sessionInterest
     * @return \Illuminate\Http\Response
     */
    public function edit(SessionInterest $sessionInterest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SessionInterest  $sessionInterest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SessionInterest $sessionInterest)
    {
        $history_message = '';

        if($request->action == 'make_participant') {
            if(!$sessionInterest->update(['is_participant' => 1])) {
                abort(500, 'Error updating record');
            }

            $history_message = "Added user: {$sessionInterest->user_info->badge_name} to {$sessionInterest->conference_session->name}";
        }

        if($request->action == 'remove_participant') {
            if(!$sessionInterest->update(['is_participant' => 0, 'is_moderator' => 0])) {
                abort(500, 'Error updating record');
            }

            $history_message = "Removed user: {$sessionInterest->user_info->badge_name} from {$sessionInterest->conference_session->name}";
        }

        if($request->action == 'make_moderator') {
            SessionInterest::where('conference_session_id', $sessionInterest->conference_session_id)
                            ->where('is_moderator', 1)
                            ->update(['is_moderator' => 0]);

            if(!$sessionInterest->update(['is_moderator' => 1])) {
                abort(500, 'Error updating record');
            }

            $history_message = "Assigned moderator: {$sessionInterest->user_info->badge_name} to {$sessionInterest->conference_session->name}";
        }

        if($request->action == 'save_staff_notes') {
            if(!$sessionInterest->update(['staff_notes' => $request->staff_notes])) {
                abort(500, 'Error updating record');
            }
        }

        if($request->action == 'save_staff_score') {
            if(!$sessionInterest->update(['staff_score' => $request->staff_score])) {
                abort(500, 'Error updating record');
            }

            $history_message = "Gave staff score of {$request->staff_score} to {$sessionInterest->user_info->badge_name} for {$sessionInterest->conference_session->name}";
        }

        if(!empty($history_message)) {
            SessionHistory::save_history(Auth::user()->id, $sessionInterest->conference_session_id, 'updated_session_participants',  $history_message);
        }

        return new SessionInterestResource($sessionInterest);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SessionInterest  $sessionInterest
     * @return \Illuminate\Http\Response
     */
    public function destroy(SessionInterest $sessionInterest)
    {
        //
    }

    /**
     * Show total session interests by category.
     *
     * @param  int  $user_id
     * @return \Illuminate\Http\Response
     */
    public function userSessionTotals(int $user_id)
    {

        //TODO Add these queries and the ones in the endpoints below to the model as methods

        $session_totals['interest'] = SessionInterest::where('user_id', $user_id)
            ->where('is_participant', 0)
            ->whereHas('conference_session', function ($query) {
                $conference_id = session('selected_conference');
                $query->where('conference_id', $conference_id);
                $query->whereNotIn('session_status_id', [6,7]);
            })
            ->count();

        $session_totals['interest'] += SessionInterest::where('user_id', $user_id)
            ->where('is_participant', 1)
            ->whereHas('conference_session', function ($query) {
                $conference_id = session('selected_conference');
                $query->where('conference_id', $conference_id);
                $query->where('type_id', 2);
                $query->whereNotIn('session_status_id', [4,5,6,7]);
            })
            ->count();

        $session_totals['panelist'] = SessionInterest::where('user_id', $user_id)
            ->where('is_participant', 1)
            ->whereHas('conference_session', function ($query) {
                $conference_id = session('selected_conference');
                $query->where('conference_id', $conference_id);
                $query->where('type_id', 1);
                $query->whereNotIn('session_status_id', [6,7]);
                })
            ->count();

        $session_totals['presenter'] = SessionInterest::where('user_id', $user_id)
            ->where('is_participant', 1)
            ->whereHas('conference_session', function ($query) {
                $conference_id = session('selected_conference');
                $query->where('conference_id', $conference_id);
                $query->where('type_id', 2);
                $query->whereIn('session_status_id', [4,5]);
            })
            ->count();

        $session_totals['other'] = SessionInterest::where('user_id', $user_id)
            ->where('is_participant', 1)
            ->whereHas('conference_session', function ($query) {
                $conference_id = session('selected_conference');
                $query->where('conference_id', $conference_id);
                $query->whereNotIn('type_id', [1,2]);
                $query->whereIn('session_status_id', [4,5]);
            })
            ->count();

        return $session_totals;


    }

    /**
     * Get a list of session interests by user.
     *
     * @param  int  $user_id
     * @return \Illuminate\Http\Response
     */
    public function userSessionsInterests(int $user_id)
    {
        if(!Gate::allows('view_admin', Auth::user()) && !Gate::allows('edit_users', $user_id)){
            abort(403, 'Not authorized');
        }

        $first = SessionInterest::where('user_id', $user_id)
            ->where('is_participant', 1)
            ->whereHas('conference_session', function ($query) {
                $conference_id = session('selected_conference');
                $query->where('conference_id', $conference_id);
                $query->where('type_id', 2);
                $query->whereNotIn('session_status_id', [4,5]);
            });

        $session_interests = SessionInterest::where('user_id', $user_id)
            ->where('is_participant', 0)
            ->whereHas('conference_session', function ($query) {
                $conference_id = session('selected_conference');
                $query->where('conference_id', $conference_id);
                $query->whereNotIn('session_status_id', [6,7]);
            })
            ->union($first);

        return  SessionInterestResource::collection($session_interests->paginate(25));


    }

    /**
     * Get a list of panels for a user.
     *
     * @param  int  $user_id
     * @return \Illuminate\Http\Response
     */
    public function userSessionsPanelist(int $user_id)
    {
        if(!Gate::allows('view_admin', Auth::user()) && !Gate::allows('edit_users', $user_id)){
            abort(403, 'Not authorized');
        }

        $session_interests = SessionInterest::where('user_id', $user_id)
            ->where('is_participant', 1)
            ->whereHas('conference_session', function ($query) {
                $conference_id = session('selected_conference');
                $query->where('conference_id', $conference_id);
                $query->where('type_id', 1);
                $query->whereNotIn('session_status_id', [6,7]);
            });

        return  SessionInterestResource::collection($session_interests->paginate(25));


    }

    /**
     * Get a list of presentations for a user.
     *
     * @param  int  $user_id
     * @return \Illuminate\Http\Response
     */
    public function userSessionsPresenter(int $user_id)
    {
        if(!Gate::allows('view_admin', Auth::user()) && !Gate::allows('edit_users', $user_id)){
            abort(403, 'Not authorized');
        }

        $session_interests = SessionInterest::where('user_id', $user_id)
            ->where('is_participant', 1)
            ->whereHas('conference_session', function ($query) {
                $conference_id = session('selected_conference');
                $query->where('conference_id', $conference_id);
                $query->where('type_id', 2);
                $query->whereIn('session_status_id', [4,5]);
            });

        return  SessionInterestResource::collection($session_interests->paginate(25));


    }

    /**
     * Get a list of presentations for a user.
     *
     * @param  int  $user_id
     * @return \Illuminate\Http\Response
     */
    public function userSessionsOther(int $user_id)
    {
        if(!Gate::allows('view_admin', Auth::user()) && !Gate::allows('edit_users', $user_id)){
            abort(403, 'Not authorized');
        }

        $session_interests = SessionInterest::where('user_id', $user_id)
            ->where('is_participant', 1)
            ->whereHas('conference_session', function ($query) {
                $conference_id = session('selected_conference');
                $query->where('conference_id', $conference_id);
                $query->whereNotIn('type_id', [1,2]);
                $query->whereIn('session_status_id', [4,5]);
            });

        return  SessionInterestResource::collection($session_interests->paginate(25));


    }
}
