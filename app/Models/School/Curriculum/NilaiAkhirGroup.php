<?php

namespace App\Models\School\Curriculum;

use Illuminate\Database\Eloquent\Model;

class NilaiAkhirGroup extends Model
{
    protected $fillable = ['id_semester', 'id_kelas', 'catatan'];

    # map
    public function NilaiAkhirGroupSimpleListMap()
    {
        return [
            'id' => $this->id,
            'semester' => Cur_getSemesterNameByID($this->id_semester),
            'kelas' => Cur_getKelasNameByID($this->id_kelas),
            'catatan' => $this->catatan,
            'dibuat' => Carbon_HumanFullDateTime($this->created_at)
        ];
    }

    # scope
    public function scopeGetLastNilaiAkhir($query)
    {
        $query->orderByDesc('id');
    }

    public function scopeGetBySemesterNow($query)
    {
        $query->where('id_semester', Cur_getActiveIDSemesterNow());
    }

    public function scopeGetAvailableNilaiAkhirGroup($query, $semester, $kelas)
    {
        $query->where([['id_semester', $semester], ['id_kelas', $kelas]]);
    }

    # relation
    public function nilaiakhir()
    {
        return $this->hasMany(NilaiAkhir::class, 'id_nilai');
    }
}
