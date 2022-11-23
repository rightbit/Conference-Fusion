<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TrackUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if(isset($this->user_id)) {
            return [
                'id' => $this->id,
                'user_id' => $this->user_id,
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
            ];
        }

        return [];

    }
}
