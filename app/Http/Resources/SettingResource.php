<?php

namespace App\Http\Resources;

use App\Models\Setting;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Setting JSON resource
 *
 * @package App\Http\Resources
 * @mixin Setting
 */
class SettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'key' => $this->key,
            'name' => $this->name,
            'description' => $this->description,
            'value' => $this->default,
        ];
    }
}
