<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Http\Resources\SettingResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingController extends ResourceController
{
    /**
     * Returns a listing of entities
     *
     * @return JsonResource
     */
    public function index(): JsonResource
    {
        return SettingResource::collection(Setting::all());
    }
}
