<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ConferenceResource extends JsonResource
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
            'name' => $this->name,
            'short_name' => $this->short_name,
            'description' => $this->description,
            'location' => $this->location,
            'url' => $this->url,
            'time_zone' => $this->time_zone,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'session_start_time' => $this->session_start_time,
            'session_end_time' => $this->session_end_time,
            'call_start_date' => $this->call_start_date,
            'call_end_date' => $this->call_end_date,
        ];
    }
}
