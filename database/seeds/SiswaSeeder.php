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
        $siswaPerKelas = 30;
        //
        $newSiswa = [];
        $getKelas = Kelas::all();
        $nisn = time();
        for ($i = 0; $i < count($getKelas); $i++) {
            for ($j = 0; $j < $siswaPerKelas; $j++) {
                $newSiswa[] = [
                    'nisn' => $nisn,
                    'nama' => $faker->name,
                    'id_kelas' => $getKelas[$i]->id
                ];
                $nisn++;
            }
        }
        Siswa::insert($newSiswa);
    }
}
