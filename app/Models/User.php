<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use MongoDB\Driver\Session;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * User Permission Functions
     */
    public function permissions()
    {
       return $this->hasMany(UserPermission::class);
    }

    public function hasPermission($permission): bool
    {
        //Allow admins always
        if ($this->permissions()->whereIn('permission', [$permission, 'admin'])->first()) {
            return true;
        }

        return false;
    }

    public function hasExactPermission($permission): bool
    {
        if ($this->permissions()->where('permission', $permission)->first()) {
            return true;
        }

        return false;
    }

    /**
     * User Info Functions
     */
    public function info()
    {
        return $this->hasOne(UserInfo::class, 'user_id', 'id')->withDefault([
            'id' => null,
        ]);
    }

    public function session_interest()
    {
        return $this->hasMany(SessionInterest::class);
    }

    /**
     * User Info Book Consignments
     */
    public function info_consignment()
    {
        return $this->hasMany(UserInfoConsignment::class, 'user_id', 'id');
    }
}
