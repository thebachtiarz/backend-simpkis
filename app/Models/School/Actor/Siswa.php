<?php

namespace App\Models\School\Actor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Siswa extends Model
{
    use SoftDeletes;

    protected $fillable = ['nisn', 'nama', 'id_kelas'];

    # map
    public function siswaSimpleListMap()
    {
        return [
            'id' => strval($this->id),
            'nisn' => $this->nisn,
            'nama' => $this->nama,
            'kelas' => Cur_getKelasNameByID($this->id_kelas)
        ];
    }

    public function siswaSimpleInfoMap()
    {
        return [
            'id' => strval($this->id),
            'nisn' => $this->nisn,
            'nama' => $this->nama,
            'kelas' => Cur_getKelasNameByID($this->id_kelas),
            'kelasid' => $this->id_kelas
        ];
    }

    # scope
    public function scopeGetUnPresensiByKegiatanToday($query, $id_kegiatan)
    {
        $query->whereNotIn('id', function ($id_siswa) use ($id_kegiatan) {
            $id_siswa->join('presensi_groups', 'presensis.id_presensi', '=', 'presensi_groups.id')
                ->select('presensis.id_siswa')
                ->from('presensis')
                ->where('presensi_groups.id_kegiatan', $id_kegiatan)
                ->whereDate('presensis.created_at', Carbon_DBdatetimeToday());
        });
    }

    public function scopeSearchSiswaByName($query, $searchname)
    {
        $query->where('nama', 'like', "%$searchname%");
    }

    public function scopeGetByKelasId($query, $id_kelas)
    {
        $query->where('id_kelas', $id_kelas);
    }

    # relation
    public function ketuakelas()
    {
        return $this->hasOne(KetuaKelas::class, 'id_siswa');
    }

    public function kelas()
    {
        return $this->belongsTo(\App\Models\School\Curriculum\Kelas::class, 'id_kelas');
    }

    public function nilaiakhir()
    {
        return $this->hasMany(\App\Models\School\Curriculum\NilaiAkhir::class, 'id_siswa');
    }

    public function presensi()
    {
        return $this->hasMany(\App\Models\School\Activity\Presensi::class, 'id_siswa');
    }

    public function nilaitambahan()
    {
        return $this->hasMany(\App\Models\School\Activity\NilaiTambahan::class, 'id_siswa');
    }
}
