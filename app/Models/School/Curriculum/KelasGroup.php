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
            'nama' => $this->nama_group
        ];
    }

    # scope

    # relation
    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'id', 'id_group');
    }
}
