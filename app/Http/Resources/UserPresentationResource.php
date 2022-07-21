<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserPresentationResource extends JsonResource
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
//        'conference_session_id' => $this->key,
//            'user_id' => $this->user_id,
//            'interest_level' => $this->interest_level,
//            'notes' => $this->notes,
//            'will_moderate' => $this->will_moderate,
//            'user' => [
//                'id' => $this->user->id,
//                'first_name' => $this->user->first_name,
//                'last_name' => $this->user->last_name,
//            ],
//            'conference_session' => [
//                'id' => $this->conference_session->id,
//                'name' => $this->conference_session->name,
//                'description' => $this->conference_session->description,
//                'participant_notes' => $this->conference_session->participant_notes,
//                'type_id' => $this->conference_session->type_id,
//            ],
        ];
    }
}
