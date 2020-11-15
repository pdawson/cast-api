<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Trait InteractsWithSettings
 * @package App\Contracts
 * @mixin Model
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
}
