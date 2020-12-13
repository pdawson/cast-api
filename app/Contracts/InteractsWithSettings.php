<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;

/**
 * Trait InteractsWithSettings
 * @package App\Contracts
 * @mixin Model
 * @property MorphToMany $settings
 */
trait InteractsWithSettings
{
    /**
     * The setting values for the particular model are stored within the pivot
     *
     * @return MorphToMany
     */
    public function settings(): MorphToMany
    {
        return $this->morphToMany(Setting::class, 'entity', 'setting_values')
            ->withPivot('value');
    }

    /**
     * Retrieves a collection of all settings (including default values)
     * overridden with the server specific values where set
     *
     * @return Collection
     */
    public function config(): Collection
    {
        $settings = Setting::query()->pluck('default', 'key');

        foreach ($this->settings as $setting) {
            $settings[$setting->key] = $setting->pivot->value;
        }

        return $settings;
    }
}
