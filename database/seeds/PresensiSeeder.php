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
        $countOfPresensi = 200;
        $newPresGroup = [];
        $newPresensi = [];
        //
        $getKegiatan = \App\Models\School\Activity\Kegiatan::getKegiatanPresensi()->get()->map->kegiatanCollectMap();
        $getSiswa = \App\Models\School\Actor\Siswa::get(['id', 'id_kelas']);
        $getIdKegiatan = Arr_pluck($getKegiatan, 'id');
        $getKetua = \App\Models\School\Actor\KetuaKelas::get(['id_siswa', 'id_kelas', 'id_user']);

        $siswaGroupKelas = [];
        foreach ($getSiswa as $key => $value) $siswaGroupKelas[$value['id_kelas']][] = $value;

        for ($i = 0; $i < $countOfPresensi; $i++) {
            $randKetua = mt_rand(0, (count($getKetua) - 1));
            $randKegiatan = Arr_random($getIdKegiatan);
            $newPresGroup[] = ['id_kegiatan' => $randKegiatan, 'id_user' => $getKetua[$randKetua]['id_user'], 'catatan' => '', 'approve' => '5'];
            for ($j = 0; $j < count($siswaGroupKelas[$getKetua[$randKetua]['id_kelas']]); $j++) {
                $newPresensi[] = [
                    'id_presensi' => $i + 1,
                    'id_semester' => Cur_getActiveIDSemesterNow(),
                    'id_kegiatan' => $randKegiatan,
                    'id_siswa' => $siswaGroupKelas[$getKetua[$randKetua]['id_kelas']][$j]['id'],
                    // if an error in array_search below, just ignore it, it's happen because dumb php-intelephense when debugging
                    'nilai' => Arr_random(Arr_pluck($getKegiatan[array_search($randKegiatan, $getIdKegiatan)]['nilai'], 'code'))
                ];
            }
        }

        PresensiGroup::insert($newPresGroup);
        foreach (array_chunk($newPresensi, 10000) as $setPresensi) Presensi::insert($setPresensi);
    }
}
