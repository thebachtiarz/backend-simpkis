<?php

namespace App\Models\School\Activity;

use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    protected $fillable = ['nama', 'nilai', 'nilai_avg', 'hari', 'waktu_mulai', 'waktu_selesai', 'akses'];

    private const canAllow = ['guru' => ['7', '5'], 'ketuakelas' => ['5']];

    # map
    public function kegiatanSimpleListMap()
    {
        return [
            'id' => strval($this->id),
            'nama' => $this->nama,
            'nilai' => $this->getNilai($this->nilai),
            'hari' => Atv_getInfoDayKegiatan($this->hari, true),
            'waktu' => "$this->waktu_mulai - $this->waktu_selesai",
            'tipe' => Atv_HumanAksesKegiatan($this->akses)
        ];
    }

    public function kegiatanSimpleInfoMap()
    {
        return [
            'id' => strval($this->id),
            'nama' => $this->nama,
            'nilai' => $this->getNilai($this->nilai),
            'hari' => Atv_getInfoDayKegiatan($this->hari, true),
            'haricode' => $this->hari,
            'mulai' => $this->waktu_mulai,
            'selesai' => $this->waktu_selesai,
            'tipe' => Atv_HumanAksesKegiatan($this->akses),
            'nilai_rerata' => strval($this->nilai_avg),
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
            'nilai' => $this->getNilaiEachKegiatan($this->nilai, 0),
            'nilai_avg' => $this->nilai_avg
        ];
    }

    # private
    private function getNilai($data, $auth = 1)
    {
        $getAuth = (bool) $auth ? User_checkStatus() : 'ketuakelas';
        $getNilai = Arr_unserialize($data);
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
        $decode = Arr_unserialize($data);
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
        $allDay = Carbon_IsWorkDayNow() ? '*' : '';
        $query->whereIn('hari', [$allDay, Carbon_DBDayNumOfWeek()])->where(function ($time) {
            $time->whereTime('waktu_mulai', '<=', Carbon_AnyTimeNow())->whereTime('waktu_selesai', '>=', Carbon_AnyTimeNow());
        });
    }

    public function scopeWhereInAllowToAccess($query, $status)
    {
        $query->whereIn('akses', self::canAllow[$status]);
    }

    public function scopeWhereAccessType($query, $type)
    {
        $query->where('akses', Atv_setAksesKegiatan($type));
    }

    public function scopeCreateNewKegiatan($query, $input, $nilai_poin)
    {
        $query->create([
            'nama' => $input->nama,
            'nilai' => serialize(Arr_collapse($nilai_poin)),
            'nilai_avg' => isset($input->nilai_avg) ? $input->nilai_avg : 0,
            'hari' => Atv_setDayKegiatan($input->hari),
            'waktu_mulai' => Carbon_AnyTimeParse($input->mulai),
            'waktu_selesai' => Carbon_AnyTimeParse($input->selesai),
            'akses' => Atv_setAksesKegiatan($input->akses)
        ]);
    }

    public function scopeUpdateKegiatan($query, $id, $input, $update_nilai)
    {
        $query->find($id)
            ->update([
                'nama' => $input->nama,
                'nilai' => serialize(Arr_collapse($update_nilai)),
                'nilai_avg' => isset($input->nilai_avg) ? $input->nilai_avg : 0,
                'hari' => Atv_setDayKegiatan($input->hari),
                'waktu_mulai' => Carbon_AnyTimeParse($input->mulai),
                'waktu_selesai' => Carbon_AnyTimeParse($input->selesai),
                'akses' => Atv_setAksesKegiatan($input->akses)
            ]);
    }

    public function scopeWithOrderAccess($query)
    {
        $query->orderBy('akses');
    }

    # relation
    public function presensi()
    {
        return $this->hasMany(\App\Models\School\Activity\Presensi::class, 'id_kegiatan');
    }

    public function nilaitambahan()
    {
        return $this->hasMany(\App\Models\School\Activity\NilaiTambahan::class, 'id_kegiatan');
    }
}
