<?php

namespace App\Models\School\Activity;

use Illuminate\Database\Eloquent\Model;

class PresensiGroup extends Model
{
    protected $fillable = ['catatan', 'approve'];

    # map
    public function presensigroupSImpleListMap()
    {
        return [
            'id' => strval($this->id),
            'catatan' => $this->catatan,
            'approve' => $this->approve,
            'waktu' => Carbon_HumanDateTime($this->created_at)
        ];
    }

    # scope
    public function scopeGetUnapprovedPresenceToday($query)
    {
        $query->where([['approve', '5'], ['created_at', '>=', Carbon_DBdatetimeToday()]]);
    }

    # relation
    public function presensi()
    {
        return $this->hasMany(\App\Models\School\Activity\Presensi::class, 'id_presensi');
    }
}
