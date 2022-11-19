<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserSessionParticipantInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $email = null;
        if($this->user_info->share_email_permission === 1){
            $email = $this->user_info->contact_email ?? $this->user->email;
        }

        return [
            'user_id' => $this->user->id,
            'first_name' => $this->user->first_name,
            'last_name' => $this->user->last_name,
            'badge_name' => $this->user_info->badge_name,
            'website' => $this->user_info->website,
            'social_data' => json_decode($this->user_info->social_data),
            'email' => $email,
            'is_moderator' => $this->is_moderator
        ];
    }
}
