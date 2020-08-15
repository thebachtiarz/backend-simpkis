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
        $countOfGroup = 200;
        $newPresGroup = [];
        $newPresensi = [];
        //
        $getKegiatan = \App\Models\School\Activity\Kegiatan::getKegiatanPresensi()->get()->map->kegiatanCollectMap();
        $getSiswa = \App\Models\School\Actor\Siswa::get(['id', 'id_kelas']);
        $getIdSemester = Arr_pluck(\App\Models\School\Curriculum\Semester::get('id'), 'id');
        $getIdKegiatan = Arr_pluck($getKegiatan, 'id');
        //
        $siswaGroupKelas = [];
        foreach ($getSiswa as $key => $value) $siswaGroupKelas[$value['id_kelas']][] = $value;
        //
        for ($i = 1; $i <= $countOfGroup; $i++) {
            $newPresGroup[] = ['catatan' => $faker->sentence(), 'approve' => Arr_random(['7', '5'])];
            $getRandIdKelas = Arr_random(array_keys($siswaGroupKelas));
            $getIdSiswa = Arr_pluck($siswaGroupKelas[$getRandIdKelas], 'id');
            $randKegiatan = (int) Arr_random($getIdKegiatan);
            $randSemester = Arr_random($getIdSemester);
            for ($j = 0; $j < count($getIdSiswa); $j++) {
                $newPresensi[] = [
                    'id_presensi' => $i,
                    'id_semester' => $randSemester,
                    'id_kegiatan' => $randKegiatan,
                    'id_siswa' => $getIdSiswa[$j],
                    // if an error in array_search below, just ignore it, it's happen because dumb php-intelephense when debugging
                    'nilai' => Arr_random(Arr_pluck($getKegiatan[array_search($randKegiatan, $getIdKegiatan)]['nilai'], 'code'))
                ];
            }
        }
        //
        PresensiGroup::insert($newPresGroup);
        foreach (array_chunk($newPresensi, 10000) as $setPresensi) Presensi::insert($setPresensi);
    }
}
