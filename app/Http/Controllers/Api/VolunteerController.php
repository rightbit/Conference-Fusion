<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VolunteerResource;
use App\Models\Volunteer;
use App\Http\Requests\VolunteerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class VolunteerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!Gate::allows('view_admin', Auth::user())){
            abort(403, 'Not authorized');
        }
       return VolunteerResource::collection(Volunteer::where('conference_id',  session('selected_conference'))
           ->with(['user' => function ($query) {
                $query->orderBy('last_name');
            }])
           ->paginate(25));
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
     *
     * @param  \App\Http\Requests\VolunteerRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(VolunteerRequest $request)
    {
        $user_id = $request->input('user_id');
        $type = $request->input('type');
        $conference_id = session('selected_conference');

        // Check for existing volunteer
        $existing = Volunteer::where('user_id', $user_id)
            ->where('conference_id', $conference_id)
            ->first();
        if ($existing) {
            return response()->json(['message' => 'User is already a volunteer for this conference.'], 422);
        }

        $volunteer = new Volunteer([
            'user_id' => $user_id,
            'conference_id' => $conference_id,
            'type' => $type,
        ]);
        $volunteer->save();
        return new VolunteerResource($volunteer);
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
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
