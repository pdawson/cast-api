<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Server;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Server JSON resource
 *
 * @package App\Http\Resources
 * @mixin Server
 */
class ServerResource extends JsonResource
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
            'name' => $this->name,
            'hostname' => $this->hostname,
            'path' => $this->path,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
