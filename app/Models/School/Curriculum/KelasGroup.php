<?php

namespace App\Models\School\Curriculum;

use Illuminate\Database\Eloquent\Model;

class KelasGroup extends Model
{
    protected $fillable = ['tingkat', 'nama_group'];

    # map

    # scope
    public function scopeGetAvailableGroupKelas($query, $tingkat, $nama)
    {
        $query->where([['tingkat', $tingkat], ['nama_group', Str_pregStringOnly($nama)]]);
    }

    # relation
    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'id', 'id_group');
    }
}
