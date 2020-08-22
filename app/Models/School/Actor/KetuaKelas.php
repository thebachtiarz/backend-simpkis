<?php

namespace App\Models\School\Actor;

use Illuminate\Database\Eloquent\Model;

class KetuaKelas extends Model
{
    protected $fillable = ['id_siswa', 'id_kelas', 'id_user'];

    # map
    public function ketuaSimpleListMap()
    {
        return [
            'id_siswa' => $this->id_siswa,
            'nama' => $this->siswa->nama,
            'kelas' => Cur_getKelasNameByID($this->id_kelas)
        ];
    }

    public function ketuaSimpleInfoMap()
    {
        return [
            'id_siswa' => $this->id_siswa,
            'nama' => $this->siswa->nama,
            'kelas' => Cur_getKelasNameByID($this->id_kelas),
            'dibuat' => Carbon_HumanDateTime($this->created_at),
            'diubah' => Carbon_HumanIntervalCreateUpdate($this->created_at, $this->updated_at)
        ];
    }

    # relation
    public function kelas()
    {
        return $this->belongsTo(\App\Models\School\Curriculum\Kelas::class, 'id_kelas');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\Auth\User::class, 'id_user');
    }
}
