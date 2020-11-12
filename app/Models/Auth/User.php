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
        'username', 'password', 'active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'username', 'password',
    ];

    # map
    public function userSimpleListMap()
    {
        return [
            'name' => $this->userbio->name,
            'status' => User_getStatusForHuman($this->userstat->status)
        ];
    }

    public function userInfoMap()
    {
        return User_getStatus($this->userstat->status) == 'ketuakelas' ? [
            'id' => strval($this->id),
            'name' => $this->userbio->name,
            'status' => User_getStatusForHuman($this->userstat->status),
            'kelas' => Cur_getKelasNameByID($this->ketuakelas->id_kelas),
            'active' => User_getActiveStatus($this->active),
            'created_at' => Carbon_HumanDateTime($this->created_at)
        ] : [
            'id' => strval($this->id),
            'name' => $this->userbio->name,
            'status' => User_getStatusForHuman($this->userstat->status),
            'active' => User_getActiveStatus($this->active),
            'created_at' => Carbon_HumanDateTime($this->created_at)
        ];
    }

    # scope
    public function scopeGetAvailableByUsername($query, $username)
    {
        $query->where('username', $username);
    }

    public function scopeGetUsersByStatus($query, $status)
    {
        $query->select('users.*')->join('user_statuses', 'users.id', '=', 'user_statuses.id')->where('status', User_setStatus($status));
    }

    # relation
    public function userbio()
    {
        return $this->hasOne(\App\Models\Auth\UserBiodata::class, 'id');
    }

    public function userstat()
    {
        return $this->hasOne(\App\Models\Auth\UserStatus::class, 'id');
    }

    public function lostpassword()
    {
        return $this->hasMany(\App\Models\Access\ForgetPassword::class, 'user_id');
    }

    public function ketuakelas()
    {
        return $this->hasOne(\App\Models\School\Actor\KetuaKelas::class, 'id_user');
    }
}
