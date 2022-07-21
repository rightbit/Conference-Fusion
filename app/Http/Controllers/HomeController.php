<?php

namespace App\Http\Controllers;

use App\Models\Conference;
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
        $conferences = Conference::where('end_date', '>=', NOW())
            ->whereNotNull('call_start_date')
            ->orderBy('start_date')
            ->get();

        $today = Carbon::today();

        foreach($conferences as &$conference) {
            if($today->gte($conference->call_start_date)) {
                $conference->call_active = true;
                ;
            }
            $conference->start_date_display = Carbon::parse($conference->start_date)->format('M j, Y');
            $conference->end_date_display = Carbon::parse($conference->end_date)->format('M j, Y');
            $conference->call_start_date_display = Carbon::parse($conference->call_start_date)->format('F j, Y');
        }

        return view('home')->with('conferences', $conferences);
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
