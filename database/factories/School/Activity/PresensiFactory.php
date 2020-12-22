<?php

namespace Database\Factories\School\Activity;

use App\Models\School\Activity\Presensi;
use Illuminate\Database\Eloquent\Factories\Factory;

class PresensiFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Presensi::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id_presensi',
            'id_semester',
            'id_siswa',
            'nilai'
        ];
    }

    public function setKegiatan(int $id)
    {
        //
    }
}
