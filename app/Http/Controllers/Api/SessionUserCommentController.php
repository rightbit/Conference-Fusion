<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SessionUserCommentRequest;
use App\Http\Resources\SessionUserCommentResource;
use App\Models\SessionInterest;
use App\Models\SessionUserComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class SessionUserCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $in_session = SessionInterest::where('user_id', Auth::user()->id)
            ->where('conference_session_id', $request->conference_session_id)
            ->where('is_participant', 1)
            ->first();

        if (!$in_session && !Gate::allows('view_admin', Auth::user())) {
            abort(403, 'Not authorized');
        }

        if($request->conference_session_id){
            return SessionUserCommentResource::collection(
                SessionUserComment::where('conference_session_id', $request->conference_session_id)
                    ->orderby('created_at')
                    ->paginate(25)
            );
        }
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
     * @param  \App\Http\Requests\SessionUserCommentRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(SessionUserCommentRequest $request)
    {
        $comment_info = new SessionUserComment($request->all());
        $comment_info->user_id  = Auth::user()->id;
        $comment_info->save();
        return new SessionUserCommentResource($comment_info);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
