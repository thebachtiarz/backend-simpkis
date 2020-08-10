<?php

namespace App\Models\School\Curriculum;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    protected $fillable = ['semester'];

    # map
    public function semesterSimpleListMap()
    {
        return [
            'semester' => $this->semester
        ];
    }

    public function semesterSimpleInfoMap()
    {
        return [
            'id' => strval($this->id),
            'semester' => $this->semester,
            'dibuat' => Carbon_HumanDateTime($this->created_at),
            'diubah' => Carbon_HumanIntervalDateTime($this->updated_at)
        ];
    }

    # scope
    public function scopeGetAvailableSemester($query, $thnsmt)
    {
        $query->where('semester', $thnsmt);
    }

    # relation
    public function nilaitambahan()
    {
        return $this->hasMany(\App\Models\School\Activity\NilaiTambahan::class, 'id_semester');
    }
}
