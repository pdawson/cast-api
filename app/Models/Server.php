<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Server Model
 *
 * @package App\Models
 */
class Server extends Model
{
    use HasFactory;

    /**
     * Attributes for mass assignment
     *
     * @var string[]
     */
    public $fillable = [
        'name',
        'hostname',
        'path',
    ];

    /**
     * A server has many sites (vHosts)
     *
     * @return HasMany
     */
    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }
}
