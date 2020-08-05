<?php

namespace App\Models\School\Actor;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $fillable = ['nisn', 'nama', 'id_kelas'];

    # relation
    public function ketuakelas()
    {
        return $this->hasOne(KetuaKelas::class, 'id_siswa', 'id');
    }
}
