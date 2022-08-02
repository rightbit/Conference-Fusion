<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserInfoDataCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $options_array = [];
        $options_map = !empty($this->options) ? explode(',', $this->options) : [];
        foreach ($options_map as $option) {
            $options_array[] = [
                'option' => $option,
                'display_option' => ucfirst($option),
            ];
        }
        return [
            'id' => $this->id,
            'type' => $this->type,
            'name' => $this->name,
            'label' => $this->label,
            'options' => $this->options,
            'options_array' => $options_array,
            'required' => $this->required,
        ];
    }
}
