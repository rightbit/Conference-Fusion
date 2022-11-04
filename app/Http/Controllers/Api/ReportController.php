<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GenericReportResource;
use App\Models\ConferenceSchedule;
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
    public function exportParticipantList(int $conference_id, Request $request)
    {
        //TODO test this and other reports to see if eloquent statements would be just as fast as a query
        $users = SessionInterest::getParticipantListReport($conference_id, $request);
        $fileName = 'participants.csv';

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('First_Name', 'Last_Name', 'Badge_Name', 'Email', 'Sessions');

        $callback = function() use($users, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($users as $user) {
                $row['First_Name']  = $user['first_name'];
                $row['Last_Name']   = $user['last_name'];
                $row['Badge_Name']  = $user['badge_name'];
                $row['Email']       = $user['email'] ?? null;

                $row['Sessions']  = '"';
                foreach ($user['sessions'] as $session) {
                    $row['Sessions'] .= $row['Sessions'] == '"' ? '' : '\n';
                    $row['Sessions'] .= $session['session_name'] . ' - ' . $session['date'] . ' ' . $session['time'];
                }
                $row['Sessions']  .= '"';

                fputcsv($file, array($row['First_Name'], $row['Last_Name'], $row['Badge_Name'], $row['Email'], $row['Sessions']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);


    }
}
