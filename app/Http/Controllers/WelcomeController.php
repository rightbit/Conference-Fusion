<?php

namespace App\Http\Controllers;

use App\Models\Conference;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class WelcomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function welcome()
    {
        //Get the next date for an open call
        $conference = Conference::find(session('selected_conference'));
        Log::debug("time".time());
        Log::debug("start_time".strtotime($conference->call_start_date));
        $start_date =  Carbon::parse($conference->call_start_date)->format('F j, Y');
        $welcome_message = 'The date for the next CFP is still being decided.';
        if($conference->call_start_date) {
            $welcome_message = "Submissions for our next conference ({$conference->name}) open on: <u>{$start_date}</u> <br />Register below for a reminder when submissions open.";
            if (strtotime($conference->call_start_date) <= time() && strtotime($conference->call_end_date) >= time()) {
                $welcome_message = "Submissions for our next conference ({$conference->name}) are now open!<br /> Register (or log in) below to participate.";
            } else if (strtotime($conference->call_end_date) < time()) {
                $welcome_message = "The submission period for our next conference ({$conference->name}) has closed,<br /> but you may register below to be notified of our next open call.";
            }
        }

        //Show a custom welcome page if the user has created one
        if(view()->exists('custom.welcome')) {
            return view('custom.welcome')->with('welcome_message', $welcome_message )->with('conference', $conference);
        }

        return view('welcome')->with('welcome_message', $welcome_message )->with('conference', $conference);
    }


}
