<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicScheduleParticipantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'firstName' => $this->info->badge_name,
            'lastName' => '',
            'biography' => $this->info->biography ?? '',
            'website' => $this->info->website ?? '',
            'image1Url' =>  !empty($this->info->profile_image) ? env('APP_URL') . $this->info->profile_image : '',
        ];
    }
}
