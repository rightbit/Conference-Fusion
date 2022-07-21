<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'info' => [
                'id' => $this->info->id,
                'badge_name' => $this->info->badge_name,
                'contact_email' => $this->info->contact_email,
                'biography' => $this->info->biography,
                'notes' => $this->info->notes,
                'website' => $this->info->website,
                'personal_data' => json_decode($this->info->personal_data),
                'social_data' => json_decode($this->info->social_data),
                'participant_data' => json_decode($this->info->participant_data),
                'profile_image' => $this->info->profile_image,
                'recording_permission' => $this->info->recording_permission ? true:false,
                'share_email_permission' => $this->info->share_email_permission ? true:false,
                'agree_to_terms' => $this->info->agree_to_terms ? true:false,
            ]
        ];
    }
}
