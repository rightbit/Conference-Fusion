<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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

        $staff_notes = null;
        if(Gate::allows('view_admin', Auth::user())){
            $staff_notes = $this->info->staff_notes;
        }

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
                'staff_notes' => $staff_notes,
                'website' => $this->info->website,
                'personal_data' => json_decode($this->info->personal_data),
                'social_data' => json_decode($this->info->social_data),
                'participant_data' => json_decode($this->info->participant_data),
                'profile_image' => $this->info->profile_image,
                'share_email_permission' => $this->info->share_email_permission ? true:false,
                'agree_to_terms' => $this->info->agree_to_terms ? true:false,
            ],

        ];
    }
}
