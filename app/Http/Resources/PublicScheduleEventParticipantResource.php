<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicScheduleEventParticipantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'eventId' => $this->event_id,
            'presenterId' => $this->user_id,
            'role' => $this->is_moderator == 1 ? 'Moderator' : '',
        ];
    }
}
