<?php

namespace App\Models\School\Curriculum;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kelas extends Model
{
    use SoftDeletes;

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
            'ketua' => (bool) $this->ketuakelas ? $this->ketuakelas->siswa->nama : '-',
            'dibuat' => Carbon_HumanDateTime($this->created_at),
            'diubah' => Carbon_AnyConvDateToTimestamp($this->kelasgroup->updated_at) > Carbon_AnyConvDateToTimestamp($this->updated_at)
                ? Carbon_HumanIntervalDateTime($this->kelasgroup->updated_at)
                : Carbon_HumanIntervalCreateUpdate($this->created_at, $this->updated_at)
        ];
    }

    # scope
    public function scopeGetAvailableKelas($query, $tingkat, $nama)
    {
        $query->join('kelas_groups', 'kelas.id_group', '=', 'kelas_groups.id')
            ->where([['kelas_groups.tingkat', $tingkat], ['nama', $nama]]);
    }

    public function scopeGetKelasByID($query, $id)
    {
        $query->where('id', $id);
    }

    # relation
    public function kelasgroup()
    {
        return $this->belongsTo(KelasGroup::class, 'id_group', 'id');
    }

    public function ketuakelas()
    {
        return $this->hasOne(\App\Models\School\Actor\KetuaKelas::class, 'id_kelas', 'id');
    }

    public function siswa()
    {
        return $this->hasMany(\App\Models\School\Actor\Siswa::class, 'id_kelas');
    }
}
