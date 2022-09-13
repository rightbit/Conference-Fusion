<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SessionInterestResource;
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
        //
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
        if($request->action == 'make_participant') {
            if(!$sessionInterest->update(['is_participant' => 1])) {
                abort(500, 'Error updating record');
            }
        }

        if($request->action == 'remove_participant') {
            if(!$sessionInterest->update(['is_participant' => 0, 'is_moderator' => 0])) {
                abort(500, 'Error updating record');
            }
        }

        if($request->action == 'make_moderator') {
            SessionInterest::where('conference_session_id', $sessionInterest->conference_session_id)
                            ->where('is_moderator', 1)
                            ->update(['is_moderator' => 0]);

            if(!$sessionInterest->update(['is_moderator' => 1])) {
                abort(500, 'Error updating record');
            }
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
}
