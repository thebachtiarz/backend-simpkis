<?php

namespace App\Models\School\Curriculum;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $fillable = ['nama', 'id_group'];

    # map
    public function kelasSimpleListMap()
    {
        return [
            'id' => strval($this->id),
            'tingkat' => $this->kelasgroup->tingkat,
            'nama' => $this->nama
        ];
    }

    public function kelasFullInfoMap()
    {
        return [
            'id' => strval($this->id),
            'tingkat' => $this->kelasgroup->tingkat,
            'nama' => $this->nama,
            'ketua' => [],
            'dibuat' => Carbon_HumanDateTime($this->created_at),
        ];
    }

    # scope
    public function scopeGetAvailableKelas($query, $tingkat, $nama)
    {
        $query->join('kelas_groups', 'kelas.id_group', '=', 'kelas_groups.id')
            ->where([['kelas_groups.tingkat', $tingkat], ['nama', $nama]]);
    }

    # relation
    public function kelasgroup()
    {
        return $this->belongsTo(KelasGroup::class, 'id_group', 'id');
    }
}
