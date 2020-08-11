<?php

namespace App\Models\School\Activity;

use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    protected $fillable = ['nama', 'nilai', 'akses'];

    # map
    public function kegiatanSimpleListMap()
    {
        return [
            'id' => strval($this->id),
            'nama' => $this->nama,
            'nilai' => $this->getNilai($this->nilai)
        ];
    }

    public function kegiatanSimpleInfoMap()
    {
        return [
            'id' => strval($this->id),
            'nama' => $this->nama,
            'nilai' => $this->getNilai($this->nilai),
            'dibuat' => Carbon_HumanDateTime($this->created_at),
            'diubah' => Carbon_HumanIntervalDateTime($this->updated_at)
        ];
    }

    public function kegiatanCollectMap()
    {
        return [
            'id' => $this->id,
            'nilai' => $this->getNilai($this->nilai, 0),
            'akses' => $this->akses
        ];
    }

    # private
    private function getNilai($data, $auth = 1)
    {
        $getAuth = (bool) $auth ? User_checkStatus() : 'ketuakelas';
        $getNilai = unserialize($data);
        $result = [];
        if ((is_array($getNilai)) && (count($getNilai) > 0)) {
            foreach ($getNilai as $keyCode => $nilai) {
                if ($getAuth == User_setStatus('guru')) $result[] = ['code' => $keyCode, 'name' => $nilai['name'], 'poin' => $nilai['poin']];
                else $result[] = ['code' => $keyCode, 'name' => $nilai['name']];
            }
        }
        return $result;
    }

    # scope
    public function scopeGetAvailableKegiatan($query, $name)
    {
        $query->where('nama', $name);
    }

    public function scopeGetKegiatanGuruOnly($query)
    {
        $query->where('akses', '7');
    }

    public function scopeGetKegiatanKetuaKelasOnly($query)
    {
        $query->where('akses', '5');
    }

    # relation
    public function presensi()
    {
        return $this->hasMany(\App\Models\School\Activity\Presensi::class, 'id_kegiatan');
    }

    public function nilaitambahan()
    {
        return $this->hasMany(NilaiTambahan::class, 'id_kegiatan');
    }
}
