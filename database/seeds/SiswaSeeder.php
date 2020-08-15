<?php

use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
use App\Models\School\Actor\Siswa;
use App\Models\School\Curriculum\Kelas;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $newSiswa = [];
        $getKelas = Arr_pluck(Kelas::all(), 'id');
        $nisn = time();
        for ($i = 0; $i < 2000; $i++) {
            $newSiswa[] = [
                'nisn' => $nisn,
                'nama' => $faker->name,
                'id_kelas' => Arr_random($getKelas)
            ];
            $nisn++;
        }
        Siswa::insert($newSiswa);
    }
}
