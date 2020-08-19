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
        $countOfPresensi = 20;
        $newPresGroup = [];
        $newPresensi = [];
        //
        $getKegiatan = \App\Models\School\Activity\Kegiatan::getKegiatanPresensi()->get()->map->kegiatanCollectMap();
        $getSiswa = \App\Models\School\Actor\Siswa::get(['id', 'id_kelas']);
        $getIdKegiatan = Arr_pluck($getKegiatan, 'id');
        //
        $getKelas = \App\Models\School\Curriculum\Kelas::all();
        $getKetua = [];
        foreach ($getKelas as $key => $value) $getKetua[] = ['id_kelas' => strval($value->id), 'id_user' => strval($value->ketuakelas->user->id)];
        //
        $siswaGroupKelas = [];
        foreach ($getSiswa as $key => $value) $siswaGroupKelas[$value['id_kelas']][] = $value;
        //
        for ($a = 0; $a < $countOfPresensi; $a++) {
            for ($i = 0; $i < count($getKetua); $i++) {
                $randKegiatan = Arr_random($getIdKegiatan);
                $newPresGroup[] = ['id_kegiatan' => $randKegiatan, 'id_user' => $getKetua[$i]['id_user'], 'catatan' => $faker->sentence(), 'approve' => '5'];
                for ($j = 0; $j < count($siswaGroupKelas[$getKetua[$i]['id_kelas']]); $j++) {
                    $newPresensi[] = [
                        'id_presensi' => strval($i + 1),
                        'id_semester' => strval(Cur_getActiveIDSemesterNow()),
                        'id_kegiatan' => strval($randKegiatan),
                        'id_siswa' => strval($siswaGroupKelas[$getKetua[$i]['id_kelas']][$j]['id']),
                        // if an error in array_search below, just ignore it, it's happen because dumb php-intelephense when debugging
                        'nilai' => Arr_random(Arr_pluck($getKegiatan[array_search($randKegiatan, $getIdKegiatan)]['nilai'], 'code'))
                    ];
                }
            }
        }
        PresensiGroup::insert($newPresGroup);
        foreach (array_chunk($newPresensi, 10000) as $setPresensi) Presensi::insert($setPresensi);
    }
}
