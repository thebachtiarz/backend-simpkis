<?php

namespace App\Models\School\Activity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    protected $fillable = ['id_presensi', 'id_semester', 'id_siswa', 'nilai'];

    # map
    public function presensiSimpleListMap()
    {
        return [
            'id' => strval($this->id),
            'siswa' => $this->siswa->nama,
            'kegiatan' => $this->presensigroup->kegiatan->nama,
            'nilai' => [
                'name' => $this->getNilaiPoin($this->presensigroup->kegiatan->nilai, $this->nilai, 'name'),
                'poin' => $this->getNilaiPoin($this->presensigroup->kegiatan->nilai, $this->nilai, 'poin')
            ],
            'dilakukan' => Carbon_HumanDateTime($this->created_at)
        ];
    }

    public function presensiDetailListMap()
    {
        return [
            'id' => strval($this->id),
            'siswa' => $this->siswa->nama,
            'siswaid' => strval($this->id_siswa),
            'kegiatan' => $this->presensigroup->kegiatan->nama,
            'nilai' => [
                'name' => $this->getNilaiPoin($this->presensigroup->kegiatan->nilai, $this->nilai, 'name'),
                'code' => $this->nilai
            ],
            'dilakukan' => Carbon_HumanDateTime($this->created_at)
        ];
    }

    public function presensiSimpleInfoMap()
    {
        return [
            'id' => strval($this->id),
            'siswa' => $this->siswa->nama,
            'kegiatan' => $this->presensigroup->kegiatan->nama,
            'kegiatanid' => strval($this->presensigroup->kegiatan->id),
            'nilai' => [
                'name' => $this->getNilaiPoin($this->presensigroup->kegiatan->nilai, $this->nilai, 'name'),
                'poin' => $this->getNilaiPoin($this->presensigroup->kegiatan->nilai, $this->nilai, 'poin'),
                'code' => $this->nilai
            ],
            'semester' => $this->semester->semester,
            'dilakukan' => Carbon_HumanDateTime($this->created_at),
            'approve' => Atv_convApproveCodeToString($this->presensigroup->approve),
            'catatan' => $this->presensigroup->catatan
        ];
    }

    public function presensiResourceMap()
    {
        return [
            'id_siswa' => $this->id_siswa,
            'id_kegiatan' => $this->presensigroup->id_kegiatan,
            'nilai' => $this->nilai
        ];
    }

    public function getNilai($data)
    {
        $getNilai = Arr_unserialize($data);
        $result = [];
        if ((is_array($getNilai)) && (count($getNilai) > 0)) $result = $getNilai;
        return $result;
    }

    # private
    private function getNilaiPoin($data, $key, $value)
    {
        return count($this->getNilai($data)) ? $this->getNilai($data)[$key][$value] : '';
    }

    # scope
    public function scopeGetPresensiResource($query, $id_semester)
    {
        $query->where('id_semester', $id_semester);
        $query->whereIn('id_presensi', function ($approve) {
            $approve->select('id')->from('presensi_groups')->where('approve', '7');
        });
    }

    public function scopeGetByKegiatanId($query, $id_kegiatan)
    {
        $query->whereIn('id_presensi', function ($q) use ($id_kegiatan) {
            $q->select('id')->from('presensi_groups')->where('id_kegiatan', $id_kegiatan);
        });
    }

    public function scopeGetByKelasId($query, $id_kelas)
    {
        $query->whereIn('id_siswa', function ($q) use ($id_kelas) {
            $q->select('id')->from('siswas')->where('id_kelas', $id_kelas);
        });
    }

    public function scopeGetBySiswaId($query, $id_siswa)
    {
        $query->where('id_siswa', $id_siswa);
    }

    # relation
    public function presensigroup()
    {
        return $this->belongsTo(\App\Models\School\Activity\PresensiGroup::class, 'id_presensi');
    }

    public function semester()
    {
        return $this->belongsTo(\App\Models\School\Curriculum\Semester::class, 'id_semester');
    }

    public function kegiatan()
    {
        return $this->belongsTo(\App\Models\School\Activity\Kegiatan::class, 'id_kegiatan');
    }

    public function siswa()
    {
        return $this->belongsTo(\App\Models\School\Actor\Siswa::class, 'id_siswa');
    }
}
