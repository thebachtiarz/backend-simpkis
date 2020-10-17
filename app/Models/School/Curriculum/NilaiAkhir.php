<?php

namespace App\Models\School\Curriculum;

use Illuminate\Database\Eloquent\Model;

class NilaiAkhir extends Model
{
    protected $fillable = ['id_nilai', 'id_semester', 'id_siswa', 'nilai_akhir'];

    # map
    public function nilaiakhirSimpleListMap()
    {
        return [
            'id' => strval($this->id),
            'nama' => $this->siswa->nama,
            'nisn' => $this->siswa->nisn,
            'kelas' => Cur_getKelasNameByID($this->siswa->id_kelas),
            'semester' => $this->semester->semester,
            'nilai_akhir' => Arr_unserialize($this->nilai_akhir)
        ];
    }

    public function nilaiakhirSimpleInfoMap()
    {
        return [
            'id' => strval($this->id),
            'nama' => $this->siswa->nama,
            'kelas' => Cur_getKelasNameByID($this->siswa->id_kelas),
            'semester' => $this->semester->semester,
            'nilai_akhir' => Arr_unserialize($this->nilai_akhir),
            'dibuat' => Carbon_HumanDateTime($this->created_at),
            'catatan' => $this->nilaiakhirgroup->catatan
        ];
    }

    public function nilaiakhirExport()
    {
        return [
            'id' => strval($this->id),
            'nisn' => $this->siswa->nisn,
            'nama' => $this->siswa->nama,
            'kelas' => Cur_getKelasNameByID($this->siswa->id_kelas),
            'semester' => $this->semester->semester,
            'nilai_akhir' => $this->nilai_akhir
        ];
    }

    # scope
    public function scopeGetResultSemesterNow($query)
    {
        $query->where('id_semester', Cur_getActiveIDSemesterNow());
    }

    # relation
    public function nilaiakhirgroup()
    {
        return $this->belongsTo(NilaiAkhirGroup::class, 'id_nilai');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'id_semester');
    }

    public function siswa()
    {
        return $this->belongsTo(\App\Models\School\Actor\Siswa::class, 'id_siswa');
    }
}
