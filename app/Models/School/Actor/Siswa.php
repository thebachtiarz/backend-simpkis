<?php

namespace App\Models\School\Actor;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $fillable = ['nisn', 'nama', 'id_kelas'];

    # map
    public function siswaSimpleInfoMap()
    {
        return [
            'id' => strval($this->id),
            'nisn' => $this->nisn,
            'nama' => $this->nama,
            'kelas' => Cur_getKelasNameByID($this->id_kelas)
        ];
    }

    # scope

    # relation
    public function ketuakelas()
    {
        return $this->hasOne(KetuaKelas::class, 'id_siswa', 'id');
    }

    public function kelas()
    {
        return $this->belongsToMany(\App\Models\School\Curriculum\Kelas::class, 'id_siswa', 'id_kelas');
    }
}
