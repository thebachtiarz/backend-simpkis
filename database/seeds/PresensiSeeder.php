<?php

use App\Models\School\Activity\Kegiatan;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
use App\Models\School\Activity\PresensiGroup;
use App\Models\School\Activity\Presensi;
use App\Models\School\Actor\Siswa;
use App\Models\School\Curriculum\Semester;

class PresensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $countOfGroup = 50;
        $countOfPress = 1000;
        $newPresGroup = [];
        $newPresensi = [];
        //
        $getKegiatan = \App\Models\School\Activity\Kegiatan::getKegiatanKetuaKelasOnly()->get()->map->kegiatanCollectMap();
        $getIdSemester = pluckArray(\App\Models\School\Curriculum\Semester::get('id'), 'id');
        $getIdSiswa = pluckArray(\App\Models\School\Actor\Siswa::get('id'), 'id');
        $getIdKegiatan = pluckArray($getKegiatan, 'id');
        //
        for ($i = 1; $i <= $countOfGroup; $i++) {
            $newPresGroup[] = ['catatan' => $faker->sentence(), 'approve' => '7'];
            for ($j = 0; $j < $countOfPress; $j++) {
                $randKegiatan = (int) randArray($getIdKegiatan);
                $newPresensi[] = [
                    'id_presensi' => $i,
                    'id_semester' => randArray($getIdSemester),
                    'id_kegiatan' => $randKegiatan,
                    'id_siswa' => randArray($getIdSiswa),
                    // if an error in array_search below, just ignore it, it's happen because dumb php-intelephense when debugging
                    'nilai' => randArray(pluckArray($getKegiatan[array_search($randKegiatan, $getIdKegiatan)]['nilai'], 'code'))
                ];
            }
        }
        //
        PresensiGroup::insert($newPresGroup);
        foreach (array_chunk($newPresensi, 10000) as $setPresensi) Presensi::insert($setPresensi);
    }
}
