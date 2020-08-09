<?php

namespace App\Models\School\Activity;

use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    protected $fillable = ['id_presensi', 'id_semester', 'id_kegiatan', 'id_siswa', 'nilai', 'approve'];

    # map

    # scope

    # relation
}
