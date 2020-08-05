<?php

namespace App\Models\School\Curriculum;

use Illuminate\Database\Eloquent\Model;

class KelasGroup extends Model
{
    protected $fillable = ['tingkat', 'nama_group'];

    # relation
    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'id', 'id_group');
    }
}
