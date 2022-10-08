<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoomRequest;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $rooms = Room::orderBy('display_order')
                ->orderBy('name')
                ->where('conference_id', $request->conference_id)
                ->when($request->filled('keyword'), function ($query) use ($request) {
                    $query->where('name', 'LIKE',"%$request->keyword%");
                });
        return RoomResource::collection($rooms->paginate(25));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\RoomRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoomRequest $request)
    {
        $room = new Room($request->all());
        $room->save();
        return new RoomResource($room);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function show(Room $room)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function edit(Room $room)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\RoomRequest  $request
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function update(RoomRequest $request, Room $room)
    {
        $room->name = $request->name;
        $room->room_number = $request->room_number;
        $room->capacity = $request->capacity;
        $room->has_av = $request->has_av;
        $room->display_order = $request->display_order;
        $room->notes = $request->notes;
        $room->save();
        $room->saveOrFail();
        return new RoomResource($room);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function destroy(Room $room)
    {
        if(!Auth::user()->hasPermission('edit_schedules')) {
            abort(403, 'Permission denied');
        }

        if($room->delete()) {
            return response('success', 200);
        }

        abort(500, 'An error occurred deleting this room');
    }
}
