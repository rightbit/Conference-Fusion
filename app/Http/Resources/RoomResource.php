<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'conference_id' => $this->id,
            'name' => $this->name,
            'room_number' => $this->room_number,
            'capacity' => $this->capacity,
            'has_av' => $this->has_av,
            'notes' => $this->notes,
            'display_order' => $this->display_order,

        ];
    }
}
