<?php

namespace App\Http\Controllers;

use App\Models\SessionInterest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
                $row['sessions']  = '';
                foreach ($user['sessions'] as $session) {
                    $row['sessions'] .= !empty($row['sessions']) ? "\r\n" : '';
                    $session_time = date("D M j, g:i a", strtotime($session['date'] . ' '. $session['time']));
                    $row['sessions'] .= " {$session_time} |  {$session['room_name']} - {$session['session_name']} ({$session['track_name']} {$session['session_type']})";
                    $row['sessions'] .= $session['is_moderator'] ? " *Moderator" : "";
                }

                fputcsv($file, array($user['first_name'], $user['last_name'],$user['badge_name'], $user['email'] ?? null, $row['sessions']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);


    }

    /**
     * Export csv of conference participants.
     *
     * @param  int  $conference_id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function exportNonParticipantList(int $conference_id, Request $request)
    {
        $users = SessionInterest::getNonParticipantListReport($conference_id); //array of objects

        $fileName = 'non-participants-contact-list.csv';
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('First_Name', 'Last_Name', 'Badge_Name', 'Email');
        $callback = function() use($users, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($users as $user) {
                fputcsv($file, array($user->first_name, $user->last_name, $user->badge_name, $user->email));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
