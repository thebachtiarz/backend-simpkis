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
            'kegiatan' => $this->kegiatan->nama
        ];
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
