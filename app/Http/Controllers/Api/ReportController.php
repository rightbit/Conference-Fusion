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
    public function sessionHistoryList(int $conference_id, Request $request)
    {
        $history = SessionHistory::where('conference_id', $conference_id)
            ->when($request->filled('keyword'), function ($query) use ($request) {
                $query->where('action_short_code', 'LIKE',"%$request->keyword%");
            })
            ->orderBy('created_at', 'DESC')
            ->paginate(25);
        return GenericReportResource::collection($history);

    }

}
