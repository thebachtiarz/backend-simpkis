<?php

namespace App\Models\Auth;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'password', 'code', 'active'
    ];

    # relation
    public function userbio()
    {
        return $this->hasOne(\App\Models\Auth\UserBiodata::class, 'code', 'code')->withDefault(['info' => 'User not found']);
    }

    public function userstat()
    {
        return $this->hasOne(\App\Models\Auth\UserStatus::class, 'code', 'code')->withDefault(['info' => 'User not found']);
    }
}
