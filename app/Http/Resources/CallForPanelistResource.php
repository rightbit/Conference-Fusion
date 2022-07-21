<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CallForPanelistResource extends JsonResource
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
            'track_id' => $this->track_id,
            'track' => $this->track->name,
            'conference_id' => $this->conference_id,
            'type_id' => $this->type_id,
            'session_type' => $this->session_type,
            'participant_notes' => $this->participant_notes,
            'staff_notes' => $this->staff_notes,
            'seed_questions' => $this->seed_questions,
            'duration_minutes' => $this->duration_minutes,
            'session_status_id' => $this->session_status_id,
            'status' => $this->status,
            'registration_required' => $this->registration_required,
            'max_registration' => $this->max_registration,
            'attendance' => $this->attendance,
            'proposed_date' => $this->proposed_date,
            'user_session_interest' => $this->user_session_interest,
        ];
    }
}
