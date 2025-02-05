<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicScheduleSponsorResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description ?? '',
            'image1Url' => $this->sponsor_image ? env('APP_URL') . $this->sponsor_image : '',
            'image2Url' => '',
            'image3Url' => '',
            'image4Url' => '',
            'image5Url' => '',
            'rules' => '',
            'organizationId' => '',
            'organization' => '',
            'thumbnailUrl' => $this->sponsor_image ? env('APP_URL') . $this->sponsor_image : '',
            'website' => $this->link ?? '',
        ];
    }
}
