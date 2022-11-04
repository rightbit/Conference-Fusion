<?php

namespace App\Http\Controllers;

use App\Models\SessionInterest;
use Illuminate\Http\Request;

class ExportController extends Controller
{

    /**
     * Export csv of conference participants.
     *
     * @param  int  $conference_id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function exportParticipantList(int $conference_id, Request $request)
    {
        $users = SessionInterest::getParticipantListReport($conference_id, $request);
        $fileName = 'participants-schedules.csv';

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

                $row['Sessions']  = '';
                foreach ($user['sessions'] as $session) {
                    $row['Sessions'] .= !empty($row['Sessions']) ? "\r\n" : '';
                    $session_time = date("D M j, g:i a", strtotime($session['date'] . ' '. $session['time']));
                    $row['Sessions'] .= "{$session['session_name']} ({$session['track_name']}) - {$session_time}";
                }


                fputcsv($file, array($row['First_Name'], $row['Last_Name'], $row['Badge_Name'], $row['Email'], $row['Sessions']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);


    }
}
