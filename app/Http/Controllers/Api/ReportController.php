<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ConferenceScheduleResource;
use App\Http\Resources\GenericReportResource;
use App\Models\ConferenceSchedule;
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
        $schedule = ConferenceSchedule::getConferenceScheduleListReport($conference_id, $request);
        return new GenericReportResource($schedule);

    }
}
