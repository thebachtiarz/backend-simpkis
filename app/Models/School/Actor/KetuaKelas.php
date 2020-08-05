<?php

namespace App\Models\School\Actor;

use Illuminate\Database\Eloquent\Model;

class KetuaKelas extends Model
{
    protected $fillable = ['id_siswa', 'id_kelas'];

    # relation
    public function kelas()
    {
        return $this->belongsTo(App\Models\School\Curriculum\Kelas::class, 'id', 'id_kelas');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id', 'id_siswa');
    }
}
