<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Class User
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property-read string $gravatar
 * @property string $remember_token
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes to append
     *
     * @var string[]
     */
    protected $appends = [
        'gravatar'
    ];

    /**
     * Gets a users Gravatar image URL
     *
     * @return string
     */
    public function getGravatarAttribute(): string
    {
        $token = md5(strtolower(trim($this->email)));
        $query = http_build_query([
            's' => 64,
            'd' => 'identicon',
            'r' => 'pg',
        ]);

        return 'https://www.gravatar.com/avatar/' . $token . '?' . $query;
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Returns a key value array with any custom claims added to the JWT
     *
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
