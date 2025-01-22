<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConferenceSponsorRequest;
use App\Http\Resources\ConferenceSponsorResource;
use App\Models\ConferenceSponsor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SponsorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sponsors = ConferenceSponsor::orderBy('display_order', 'ASC')
            ->orderBy('name', 'ASC')
            ->where('conference_id', $request->conference_id);
        return ConferenceSponsorResource::collection($sponsors->paginate(25));
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
     * @param \App\Http\Requests\ConferenceSponsorRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ConferenceSponsorRequest $request)
    {
        $sponsor = new ConferenceSponsor($request->all());
        $sponsor->save();
        return new ConferenceSponsorResource($sponsor);
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
     * @param \App\Http\Requests\ConferenceSponsorRequest $request
     * @param \App\Models\ConferenceSponsor $sponsor
     * @return \Illuminate\Http\Response
     */
    public function update(ConferenceSponsorRequest $request, ConferenceSponsor $sponsor)
    {
        $sponsor->update($request->all());
        return new ConferenceSponsorResource($sponsor);
    }

    /**
     * Remove the specified resource from storage.
     * @param \App\Models\ConferenceSponsor $sponsor
     * @return \Illuminate\Http\Response
     */
    public function destroy(ConferenceSponsor $sponsor)
    {
        if(!Auth::user()->hasPermission('edit_conference')) {
            abort(403, 'Permission denied');
        }

        if($sponsor->delete()) {
            return response()->json(['message' => 'Sponsor deleted successfully']);
        }

        abort(500, 'An error occurred deleting this room');
    }
}
