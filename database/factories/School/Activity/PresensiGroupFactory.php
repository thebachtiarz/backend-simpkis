<?php

namespace Database\Factories\School\Activity;

use App\Models\School\Activity\PresensiGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class PresensiGroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PresensiGroup::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            // 'id_kegiatan',
            // 'id_user',
            // 'catatan',
            // 'approve'
        ];
    }
}
