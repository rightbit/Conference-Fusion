<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class SiteConfigResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if(Gate::allows('view_admin', Auth::user())){
            return [
                'id' => $this->id,
                'key' => $this->key,
                'value' => $this->value,
                'description' => $this->description,
                'user' => [
                    'id' => $this->user->id,
                    'first_name' => $this->user->first_name,
                    'last_name' => $this->user->last_name,
                ],
            ];
        }

        return [
            'key' => $this->key,
            'value' => $this->value,
        ];

    }
}
