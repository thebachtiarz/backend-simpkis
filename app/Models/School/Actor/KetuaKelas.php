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
            'id' => strval($this->id),
            'nama' => $this->siswa->nama,
            'kelas' => Cur_getKelasNameByID($this->id_kelas)
        ];
    }

    public function ketuaSimpleInfoMap()
    {
        return [
            'id' => strval($this->id),
            'nama' => $this->siswa->nama,
            'siswaid' => strval($this->id_siswa),
            'kelas' => Cur_getKelasNameByID($this->id_kelas),
            'kelasid' => strval($this->id_kelas),
            'dibuat' => Carbon_HumanDateTime($this->created_at),
            'diubah' => Carbon_HumanIntervalCreateUpdate($this->created_at, $this->updated_at)
        ];
    }

    # scope
    public function scopeCreateNewKetuaKelas($query, $id_siswa, $id_kelas, $id_user)
    {
        $query->create(['id_siswa' => $id_siswa, 'id_kelas' => $id_kelas, 'id_user' => $id_user]);
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
