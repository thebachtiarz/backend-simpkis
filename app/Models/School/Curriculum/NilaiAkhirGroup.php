<?php

namespace App\Models\School\Curriculum;

use Illuminate\Database\Eloquent\Model;

class NilaiAkhirGroup extends Model
{
    protected $fillable = ['id_semester', 'id_kelas', 'catatan'];

    # map

    # scope
    public function scopeGetAvailableNilaiAkhirGroup($query, $semester, $kelas)
    {
        $query->where([['id_semester', $semester], ['id_kelas', $kelas]]);
    }

    # relation
}
