<?php

namespace App\Models\School\Activity;

use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    protected $fillable = ['nama', 'nilai', 'hari', 'waktu_mulai', 'waktu_selesai', 'akses'];

    # map
    public function kegiatanSimpleListMap()
    {
        return [
            'id' => strval($this->id),
            'nama' => $this->nama,
            'nilai' => $this->getNilai($this->nilai),
            'hari' => Atv_getInfoDayKegiatan($this->hari, true),
            'waktu' => "$this->waktu_mulai - $this->waktu_selesai"
        ];
    }

    public function kegiatanSimpleInfoMap()
    {
        return [
            'id' => strval($this->id),
            'nama' => $this->nama,
            'nilai' => $this->getNilai($this->nilai),
            'hari' => Atv_getInfoDayKegiatan($this->hari, true),
            'mulai' => $this->waktu_mulai,
            'selesai' => $this->waktu_selesai,
            'dibuat' => Carbon_HumanDateTime($this->created_at),
            'diubah' => Carbon_HumanIntervalCreateUpdate($this->created_at, $this->updated_at)
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

    public function kegiatanResourceMap()
    {
        return [
            'id' => $this->id,
            'nilai' => $this->getNilaiEachKegiatan($this->nilai, 0)
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

    private function getNilaiEachKegiatan($data)
    {
        $decode = unserialize($data);
        $result = [];
        if ((is_array($decode)) && (count($decode) > 0)) {
            foreach ($decode as $key => $nilai) {
                $result[$key] = $nilai['poin'];
            }
        }
        return $result;
    }

    # scope
    public function scopeGetAvailableKegiatan($query, $name)
    {
        $query->where('nama', $name);
    }

    public function scopeGetKegiatanTambahan($query)
    {
        $query->where('akses', Atv_setAksesKegiatan('tambahan'));
    }

    public function scopeGetKegiatanPresensi($query)
    {
        $query->where('akses', Atv_setAksesKegiatan('presensi'));
    }

    public function scopeGetAvailablePresensiNow($query)
    {
        $query->whereIn('hari', ['*', Carbon_DBDayNumOfWeek()])->where(function ($time) {
            $time->whereTime('waktu_mulai', '<=', Carbon_AnyTimeNow())->whereTime('waktu_selesai', '>=', Carbon_AnyTimeNow());
        });
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
