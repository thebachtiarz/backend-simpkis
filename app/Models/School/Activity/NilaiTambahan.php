<?php

namespace App\Models\School\Activity;

use Illuminate\Database\Eloquent\Model;

class NilaiTambahan extends Model
{
    protected $fillable = ['id_semester', 'id_siswa', 'id_kegiatan', 'nilai'];

    # map
    public function nilaitambahanSimpleListMap()
    {
        return [
            'semester' => $this->semester->semester,
            'siswa' => $this->siswa->nama,
            'kegiatan' => $this->kegiatan->nama,
            'dilakukan' => Carbon_HumanDateTime($this->created_at)
        ];
    }

    public function nilaitambahanInfoSimpleMap()
    {
        return [
            'id' => strval($this->id),
            'semester' => $this->semester->semester,
            'siswa' => $this->siswa->nama,
            'kegiatan' => $this->kegiatan->nama,
            'nilai' => $this->getNilaiPoin($this->kegiatan->nilai, $this->nilai, 'poin'),
            'dilakukan' => Carbon_HumanDateTime($this->created_at)
        ];
    }

    public function nilaitambahanResourceMap()
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

    # relation
    public function semester()
    {
        return $this->belongsTo(\App\Models\School\Curriculum\Semester::class, 'id_semester');
    }

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'id_kegiatan');
    }

    public function siswa()
    {
        return $this->belongsTo(\App\Models\School\Actor\Siswa::class, 'id_siswa');
    }
}
