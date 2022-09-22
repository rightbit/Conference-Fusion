<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SessionInterestResource extends JsonResource
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
            'conference_session_id' => $this->conference_session_id,
            'user_id' => $this->user_id,
            'interest_level' => $this->interest_level,
            'experience_level' => $this->experience_level,
            'panel_role' => $this->panel_role,
            'notes' => $this->notes,
            'will_moderate' => $this->will_moderate,
            'is_participant' => $this->is_participant,
            'is_moderator' => $this->is_moderator,
            'user' => [
                'id' => $this->user->id,
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
                'email' => $this->user->email,
            ],
            'user_info' => [
                'badge_name' => $this->user_info->badge_name,
                'biography' => $this->user_info->biography,
                'notes' => $this->user_info->notes,
            ],
            'conference_session' => [
                'id' => $this->conference_session->id,
                'name' => $this->conference_session->name,
                'description' => $this->conference_session->description,
                'participant_notes' => $this->conference_session->participant_notes,
                'type_id' => $this->conference_session->type_id,
                'track_id' => $this->conference_session->track_id,
                'track_name' => $this->conference_session->track->name,
            ],
        ];
    }
}
