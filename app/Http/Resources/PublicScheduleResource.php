<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $startTime = date("Y-m-d H:i:s", strtotime($this->date .' '. $this->time));
        $endTime = date("Y-m-d H:i:s", strtotime($startTime ." + 45 minutes"));

        return [
            'id' => $this->id,
            'location_id' => $this->room_id,
            'room' => $this->room->name,
            'name' => $this->session->name,
            'description' => $this->session->description,
            'tracks' => [$this->track_id],
            'startTime' => $startTime,
            'endTime' => $endTime,
            'type' => $this->session->session_type->name,
            'presenterIds' => PublicScheduleParticipantIdsResource::collection($this->session->session_participants)

        ];
    }
}
