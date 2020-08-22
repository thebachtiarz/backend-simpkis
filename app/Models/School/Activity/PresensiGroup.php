<?php

namespace App\Models\School\Activity;

use Illuminate\Database\Eloquent\Model;

class PresensiGroup extends Model
{
    protected $fillable = ['id_kegiatan', 'id_user', 'catatan', 'approve'];

    # map
    public function presensigroupSimpleListMap()
    {
        return [
            'id' => strval($this->id),
            'kegiatan' => $this->kegiatan->nama,
            'pengabsen' => $this->user->userbio->name,
            'catatan' => $this->catatan,
            'approve' => Atv_convApproveCodeToString($this->approve),
            'waktu' => Carbon_HumanDateTime($this->created_at)
        ];
    }

    # scope
    public function scopeGetUnapprovedPresenceToday($query)
    {
        $query->where([['approve', '5'], ['created_at', '>=', Carbon_DBdatetimeToday()]]);
    }

    public function scopeGetKetuaKelasPresensiToday($query, $id_user)
    {
        $query->where([['id_user', $id_user], ['created_at', '>=', Carbon_DBdatetimeToday()]]);
    }

    # relation
    public function presensi()
    {
        return $this->hasMany(\App\Models\School\Activity\Presensi::class, 'id_presensi');
    }

    public function kegiatan()
    {
        return $this->belongsTo(\App\Models\School\Activity\Kegiatan::class, 'id_kegiatan');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\Auth\User::class, 'id_user');
    }
}
