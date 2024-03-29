<?php

namespace App\Models\School\Activity;

use Illuminate\Database\Eloquent\Model;

class NilaiTambahan extends Model
{
    protected $fillable = ['id_semester', 'id_siswa', 'id_kegiatan', 'nilai'];

    # map
    public function nilaitambahanSimpleListMap()
    {
        return [
            'semester' => $this->semester->semester,
            'siswa' => $this->siswa->nama,
            'kegiatan' => $this->kegiatan->nama,
            'dilakukan' => Carbon_HumanDateTime($this->created_at)
        ];
    }

    public function nilaitambahanInfoSimpleMap()
    {
        return [
            'id' => strval($this->id),
            'semester' => $this->semester->semester,
            'siswa' => $this->siswa->nama,
            'kegiatan' => $this->kegiatan->nama,
            'kegiatanid' => strval($this->kegiatan->id),
            'nilai' => [
                'code' => $this->nilai,
                'name' => $this->getNilaiPoin($this->kegiatan->nilai, $this->nilai, 'name'),
                'poin' => $this->getNilaiPoin($this->kegiatan->nilai, $this->nilai, 'poin')
            ],
            'dilakukan' => Carbon_HumanDateTime($this->created_at)
        ];
    }

    public function nilaitambahanResourceMap()
    {
        return [
            'id_siswa' => $this->id_siswa,
            'id_kegiatan' => $this->id_kegiatan,
            'nilai' => $this->nilai
        ];
    }

    # private
    private function getNilai($data)
    {
        $getNilai = Arr_unserialize($data);
        $result = [];
        if ((is_array($getNilai)) && (count($getNilai) > 0)) $result = $getNilai;
        return $result;
    }

    private function getNilaiPoin($data, $key, $value)
    {
        return count($this->getNilai($data)) ? (in_array($key, array_keys($this->getNilai($data))) ? $this->getNilai($data)[$key][$value] : '-') : '';
    }

    # scope
    public function scopeGetNilaiTambahanResource($query, $id_semester)
    {
        $query->where('id_semester', $id_semester);
    }

    public function scopeGetBySemesterId($query, $id_semester = '')
    {
        $query->where('id_semester', isset($id_semester) ? $id_semester : Cur_getActiveIDSemesterNow());
    }

    public function scopeGetByKegiatanId($query, $id_kegiatan)
    {
        $query->where('id_kegiatan', $id_kegiatan);
    }

    public function scopeGetBySiswaId($query, $id_siswa)
    {
        $query->where('id_siswa', $id_siswa);
    }

    public function scopeCreateNewNilaiTambahan($query, $input)
    {
        $query->create(['id_semester' => Cur_getActiveIDSemesterNow(), 'id_siswa' => $input->siswaid, 'id_kegiatan' => $input->kegiatanid, 'nilai' => $input->nilai]);
    }

    public function scopeUpdateNilaiTambahan($query, $id, $input)
    {
        $query->find($id)->update(['id_kegiatan' => $input->kegiatanid, 'nilai' => $input->nilai]);
    }

    # relation
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
