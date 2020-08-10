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

    # private
    private function getNilai($data)
    {
        $getNilai = unserialize($data);
        $result = [];
        if ((is_array($getNilai)) && (count($getNilai) > 0)) {
            foreach ($getNilai as $keyCode => $nilai) {
                if (User_checkStatus() == User_setStatus('guru')) {
                    $result[] = ['code' => $keyCode, 'name' => $nilai['name'], 'poin' => $nilai['poin']];
                } else {
                    $result[] = ['code' => $keyCode, 'name' => $nilai['name']];
                }
            }
        }
        return $result;
    }

    # scope
    public function scopeGetAvailableKegiatan($query, $name)
    {
        $query->where('nama', $name);
    }

    # relation
}
