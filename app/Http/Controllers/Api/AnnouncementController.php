<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ConferenceAnnouncementResource;
use App\Http\Requests\ConferenceAnnouncementRequest;
use App\Models\ConferenceAnnouncement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $announcements = ConferenceAnnouncement::orderBy('pinned', 'DESC')
            ->orderBy('display_date', 'DESC')
            ->where('conference_id', $request->conference_id);
        return ConferenceAnnouncementResource::collection($announcements->paginate(25));
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
     * @param  \App\Http\Requests\ConferenceAnnouncementRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ConferenceAnnouncementRequest $request)
    {
        // save the announcement
        $announcement = new ConferenceAnnouncement($request->all());
        $announcement->save();
        return new ConferenceAnnouncementResource($announcement);
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
     * @param \App\Http\Requests\ConferenceAnnouncementRequest $request
     * @param \App\Models\ConferenceAnnouncement $announcement
     * @return \Illuminate\Http\Response
     */
    public function update(ConferenceAnnouncementRequest $request, ConferenceAnnouncement $announcement)
    {
        // save the announcement
        $announcement->update($request->all());
        return new ConferenceAnnouncementResource($announcement);

    }

    /**
     * Remove the specified resource from storage.
     * @param \App\Models\ConferenceAnnouncement $announcement
     * @return \Illuminate\Http\Response
     */
    public function destroy(ConferenceAnnouncement $announcement)
    {
        if(!Auth::user()->hasPermission('edit_conference')) {
            abort(403, 'Permission denied');
        }

        if($announcement->delete()) {
            return response()->json(['message' => 'Announcement deleted'], 200);
        }

        abort(500, 'An error occurred deleting this room');
    }
}
