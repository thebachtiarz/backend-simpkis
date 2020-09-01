<?php

namespace App\Models\School\Curriculum;

use Illuminate\Database\Eloquent\Model;

class KelasGroup extends Model
{
    protected $fillable = ['tingkat', 'nama_group'];

    # map
    public function kelasgroupSimpleListMap()
    {
        return [
            'id' => strval($this->id),
            'tingkat' => $this->tingkat,
            'nama' => $this->nama_group
        ];
    }

    # scope
    public function scopeSearchKelasGroupByName($query, $nama, $tingkat = '')
    {
        $query->where('nama_group', 'like', "%{$nama}%");
        if (isset($tingkat)) $query->where('tingkat', $tingkat);
    }

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
