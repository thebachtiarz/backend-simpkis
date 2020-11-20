<?php

namespace App\Models\School\Curriculum;

use Illuminate\Database\Eloquent\Model;

class KelasGroup extends Model
{
    protected $fillable = ['tingkat', 'nama_group', 'status'];

    # map
    public function kelasgroupSimpleListMap()
    {
        return [
            'id' => strval($this->id),
            'tingkat' => $this->tingkat,
            'nama' => $this->nama_group,
            'status' => ucfirst(Cur_getKelasStatus($this->status)),
            'jmlkelas' => strval($this->kelas->count()),
            'diperbarui' => Carbon_HumanIntervalCreateUpdate($this->created_at, $this->updated_at)
        ];
    }

    # scope
    public function scopeSearchKelasGroupByName($query, $nama, $tingkat = '')
    {
        $query->where('nama_group', 'like', "%{$nama}%");
        $query->where('status', Cur_setKelasStatus('active'));
        if (isset($tingkat)) $query->where('tingkat', $tingkat);
    }

    public function scopeGetAvailableGroupKelas($query, $tingkat, $nama)
    {
        $query->where([['tingkat', $tingkat], ['nama_group', Str_pregStringOnly($nama)]]);
    }

    public function scopeCreateNewKelasGroup($query, $input)
    {
        // return because the result is using in KelasManagement->kelasStore
        return $query->create([
            'tingkat' => $input->tingkat,
            'nama_group' => Str_pregStringOnly($input->nama),
            'status' => Cur_setKelasStatus('active')
        ]);
    }

    public function scopeGetActiveKelas($query)
    {
        $query->where('status', Cur_setKelasStatus('active'));
    }

    public function scopeGetGraduatedKelas($query)
    {
        $query->where('status', Cur_setKelasStatus('graduated'));
    }

    # relation
    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'id_group', 'id');
    }
}
