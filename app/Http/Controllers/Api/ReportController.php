<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GenericReportResource;
use App\Models\ConferenceSchedule;
use App\Models\SessionHistory;
use App\Models\SessionInterest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  int  $conference_id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function scheduleList(int $conference_id, Request $request)
    {
        //TODO test this and other reports to see if eloquent statements would be just as fast as a query
        $schedule = ConferenceSchedule::getConferenceScheduleListReport($conference_id, $request);
        return new GenericReportResource($schedule);

    }


    /**
     * Display a listing of the resource.
     *
     * @param  int  $conference_id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function participantList(int $conference_id, Request $request)
    {
        //TODO test this and other reports to see if eloquent statements would be just as fast as a query
        $users = SessionInterest::getParticipantListReport($conference_id, $request);
        return new GenericReportResource($users);

    }


    /**
     * Display a listing of the resource.
     *
     * @param  int  $conference_id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function potentialsList(int $conference_id, Request $request)
    {
        $users = SessionInterest::getPotentialsListReport($conference_id, $request->type_id, $request->sort, $request->desc);
        return new GenericReportResource($users);

    }

    /**
     * Display a listing of the resource.
     *
     * @param  int  $conference_id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sessionHistoryList(int $conference_id, Request $request)
    {
        $history = SessionHistory::with('user')
            ->where('conference_id', $conference_id)
            ->when($request->filled('short_code'), function ($query) use ($request) {
                $query->where('action_short_code', $request->short_code);
            })
            ->orderBy('created_at', 'DESC')
            ->paginate(25);
        return GenericReportResource::collection($history);

    }

}
