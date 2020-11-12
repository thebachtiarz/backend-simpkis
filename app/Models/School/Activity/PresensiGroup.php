<?php

namespace App\Models\School\Activity;

use Illuminate\Database\Eloquent\Model;

class PresensiGroup extends Model
{
    protected $fillable = ['id_kegiatan', 'id_user', 'catatan', 'approve'];

    # map
    public function presensigroupSimpleListMap()
    {
        return [
            'id' => strval($this->id),
            'kegiatan' => $this->kegiatan->nama,
            'kelas' => Cur_getKelasNameByID($this->presensi[0]->siswa->kelas->id),
            'approve' => Atv_convApproveCodeToString($this->approve),
            'waktu' => Carbon_HumanDateTime($this->created_at)
        ];
    }

    public function presensigroupSimpleInfoMap()
    {
        return [
            'id' => strval($this->id),
            'kegiatan' => $this->kegiatan->nama,
            'pengabsen' => $this->user->userbio->name,
            'catatan' => $this->catatan,
            'approve' => Atv_convApproveCodeToString($this->approve),
            'waktu' => Carbon_HumanDateTime($this->created_at)
        ];
    }

    # scope
    public function scopeGetUnapprovedPresenceToday($query)
    {
        $query->where('approve', '5')->whereDate('created_at', Carbon_DBdatetimeToday());
    }

    public function scopeGetUnapprovedPresenceByDate($query, $date)
    {
        $query->where('approve', '5')->whereDate('created_at', Carbon_DBConvertDateTime($date));
    }

    public function scopeGetKetuaKelasPresensiToday($query, $id_kelas)
    {
        $query->whereIn('id', function ($q) use ($id_kelas) {
            $q->select('id_presensi')
                ->from('presensis')
                ->join('siswas', 'presensis.id_siswa', '=', 'siswas.id')
                ->where('siswas.id_kelas', $id_kelas)
                ->whereDate('presensis.created_at', Carbon_DBdatetimeToday())
                ->groupBy('presensis.id_presensi');
        });
    }

    public function scopeGetKetuaKelasPresensiByDate($query, $id_kelas, $date)
    {
        $query->whereIn('id', function ($q) use ($id_kelas, $date) {
            $q->select('id_presensi')
                ->from('presensis')
                ->join('siswas', 'presensis.id_siswa', '=', 'siswas.id')
                ->where('siswas.id_kelas', $id_kelas)
                ->whereDate('presensis.created_at', Carbon_DBConvertDateTime($date))
                ->groupBy('presensis.id_presensi');
        });
    }

    # relation
    public function presensi()
    {
        return $this->hasMany(\App\Models\School\Activity\Presensi::class, 'id_presensi');
    }

    public function kegiatan()
    {
        return $this->belongsTo(\App\Models\School\Activity\Kegiatan::class, 'id_kegiatan');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\Auth\User::class, 'id_user');
    }
}
