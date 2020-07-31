<?php

namespace App\Models\Auth;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'password', 'code', 'active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'username', 'password',
    ];

    # scope
    public function scopeGetUsersByStatus($query, $status)
    {
        $query->select('users.*')->join('user_statuses', 'users.code', '=', 'user_statuses.code')->where('status', User_setStatus($status));
    }

    # relation
    public function userbio()
    {
        return $this->hasOne(\App\Models\Auth\UserBiodata::class, 'code', 'code');
    }

    public function userstat()
    {
        return $this->hasOne(\App\Models\Auth\UserStatus::class, 'code', 'code');
    }

    public function lostpassword()
    {
        return $this->hasMany(\App\Models\Access\ForgetPassword::class, 'code', 'code');
    }
}
