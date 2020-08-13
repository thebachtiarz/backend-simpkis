<?php

namespace App\Models\School\Activity;

use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    protected $fillable = ['id_presensi', 'id_semester', 'id_kegiatan', 'id_siswa', 'nilai'];

    # map
    public function presensiSimpleListMap()
    {
        return [
            'id' => strval($this->id),
            'siswa' => $this->siswa->nama,
            'kegiatan' => $this->kegiatan->nama,
            'nilai' => $this->getNilaiPoin($this->kegiatan->nilai, $this->nilai, 'name'),
            'dilakukan' => Carbon_HumanDateTime($this->created_at)
        ];
    }

    public function presensiSimpleInfoMap()
    {
        return [
            'id' => strval($this->id),
            'siswa' => $this->siswa->nama,
            'kegiatan' => $this->kegiatan->nama,
            'nilai' => $this->getNilaiPoin($this->kegiatan->nilai, $this->nilai, 'name'),
            'dilakukan' => Carbon_HumanDateTime($this->created_at),
            'approve' => Cur_convApproveCodeToString($this->presensigroup->approve),
            'catatan' => $this->presensigroup->catatan
        ];
    }

    public function presensiResourceMap()
    {
        return [
            'id_siswa' => $this->id_siswa,
            'id_kegiatan' => $this->id_kegiatan,
            'nilai' => $this->nilai
        ];
    }

    # private
    public function getNilai($data)
    {
        $getNilai = unserialize($data);
        $result = [];
        if ((is_array($getNilai)) && (count($getNilai) > 0)) $result = $getNilai;
        return $result;
    }

    public function getNilaiPoin($data, $key, $value)
    {
        return count($this->getNilai($data)) ? $this->getNilai($data)[$key][$value] : '';
    }

    # scope
    public function scopeGetPresensiResource($query, $id_semester)
    {
        $query->where('id_semester', $id_semester);
    }

    # relation
    public function presensigroup()
    {
        return $this->belongsTo(\App\Models\School\Activity\PresensiGroup::class, 'id_presensi');
    }

    public function semester()
    {
        return $this->belongsTo(\App\Models\School\Curriculum\Semester::class, 'id_semester');
    }

    public function kegiatan()
    {
        return $this->belongsTo(\App\Models\School\Activity\Kegiatan::class, 'id_kegiatan');
    }

    public function siswa()
    {
        return $this->belongsTo(\App\Models\School\Actor\Siswa::class, 'id_siswa');
    }
}
