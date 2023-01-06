<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserScheduledSessionInfoResource extends JsonResource
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
            'description' => $this->description,
            'seed_questions' => $this->seed_questions,
            'duration_minutes' => $this->duration_minutes,
            'conference_name' => $this->conference->name,
            'track_name' => $this->track->name,
            'type_name' => $this->session_type->name,
            'conference_schedules' => ConferenceScheduleResource::collection($this->conference_schedule),
            'session_participants' => UserSessionParticipantInfoResource::collection($this->session_participants),
        ];
    }
}
