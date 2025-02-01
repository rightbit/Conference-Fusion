<?php

namespace App\Http\Controllers;

use App\Http\Resources\CallForPanelistResource;
use App\Http\Resources\UserScheduleConferenceResource;
use App\Http\Resources\UserScheduleSessionResource;
use App\Models\Conference;
use App\Models\ConferenceSchedule;
use App\Models\ConferenceSession;
use App\Models\SessionInterest;
use App\Models\SiteConfig;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $home_page_message = SiteConfig::where('key', 'message_home_page')->first();
        $mass_signing_enabled = SiteConfig::where('key', 'book_signing_module')->first();

        $conferences = Conference::whereRaw('end_date >= CURDATE() + INTERVAL 1 DAY')
            ->whereNotNull('call_start_date')
            ->orderBy('start_date')
            ->get();

        $today = Carbon::today(env('APP_TIMEZONE', 'UTC'));

        $user_sessions = [];
        foreach($conferences as &$conference) {
            if($today->gte($conference->call_start_date) && $today->lte($conference->call_end_date)) {
                $conference->call_active = true;
            }
            $conference->slug = str_replace(" ", "-", $conference->short_name);
            $conference->start_date_display = Carbon::parse($conference->start_date)->format('M j, Y');
            $conference->end_date_display = Carbon::parse($conference->end_date)->format('M j, Y');
            $conference->call_start_date_display = Carbon::parse($conference->call_start_date)->format('F j, Y');
            $conference->call_end_date_display = Carbon::parse($conference->call_end_date)->format('F j, Y');
            $conference_sessions = SessionInterest::getUserSchedule(Auth::user()->id, $conference->id);
            $user = User::find(Auth::user()->id);

            if(count($conference_sessions) > 0) {
                $user_sessions[] = [
                    'conference_info' => $conference,
                    'session_info' => $conference_sessions,
                ];
            }
        }
        //TODO Add list of upcoming sessions, requested and approved.

        return view('home')
            ->with('user', $user)
            ->with('conferences', $conferences)
            ->with('home_page_message', $home_page_message->value ?? '')
            ->with('mass_signing_enabled', $mass_signing_enabled->value ?? 0)
            ->with('user_sessions', $user_sessions);
    }

    /**
     * Show the application dashboard.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function conferenceSelect($id)
    {
        if (! Gate::allows('view_admin', Auth::user())) {
            abort(403, 'Not authorized');
        }

        if(Conference::find($id)) {
            session(['selected_conference' => $id]);
            return back()->withInput();
        }

        abort(404, 'Conference not found');

    }
}
