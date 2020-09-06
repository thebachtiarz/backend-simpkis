<?php

use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
use App\Models\School\Activity\PresensiGroup;
use App\Models\School\Activity\Presensi;

class PresensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $keg01PerSmt = 80;
        $keg02PerSmt = 20;
        $keg03PerSmt = 100;
        //
        $newPresGroup = [];
        $newPresensi = [];
        //
        $getKetua = \App\Models\School\Actor\KetuaKelas::all();
        $getKegiatan = \App\Models\School\Activity\Kegiatan::getKegiatanPresensi()->get()->map->kegiatanCollectMap();
        //

        for ($i = 0; $i < count($getKetua); $i++) {
            $idPresensi = 1;
            //
            // presensi kegiatan id 1
            for ($j = 0; $j < $keg01PerSmt; $j++) {
                $newPresGroup[] = ['id_kegiatan' => '1', 'id_user' => $getKetua[$i]->id_user, 'catatan' => $faker->sentence(), 'approve' => '7'];
                for ($ja = 0; $ja < count($getKetua[$i]->kelas->siswa); $ja++) {
                    $newPresensi[] = [
                        'id_presensi' => $idPresensi,
                        'id_semester' => Cur_getActiveIDSemesterNow(),
                        'id_kegiatan' => '1',
                        'id_siswa' => $getKetua[$i]->kelas->siswa[$ja]->id,
                        'nilai' => Arr_random(Arr_pluck($getKegiatan[0]['nilai'], 'code'))
                    ];
                }
                $idPresensi++;
            }
            //
            // presensi kegiatan id 2
            for ($k = 0; $k < $keg02PerSmt; $k++) {
                $newPresGroup[] = ['id_kegiatan' => '2', 'id_user' => $getKetua[$i]->id_user, 'catatan' => $faker->sentence(), 'approve' => '7'];
                for ($ka = 0; $ka < count($getKetua[$i]->kelas->siswa); $ka++) {
                    $newPresensi[] = [
                        'id_presensi' => $idPresensi,
                        'id_semester' => Cur_getActiveIDSemesterNow(),
                        'id_kegiatan' => '2',
                        'id_siswa' => $getKetua[$i]->kelas->siswa[$ka]->id,
                        'nilai' => Arr_random(Arr_pluck($getKegiatan[1]['nilai'], 'code'))
                    ];
                }
                $idPresensi++;
            }
            //
            // presensi kegiatan id 3
            for ($l = 0; $l < $keg03PerSmt; $l++) {
                $newPresGroup[] = ['id_kegiatan' => '3', 'id_user' => $getKetua[$i]->id_user, 'catatan' => $faker->sentence(), 'approve' => '7'];
                for ($la = 0; $la < count($getKetua[$i]->kelas->siswa); $la++) {
                    $newPresensi[] = [
                        'id_presensi' => $idPresensi,
                        'id_semester' => Cur_getActiveIDSemesterNow(),
                        'id_kegiatan' => '3',
                        'id_siswa' => $getKetua[$i]->kelas->siswa[$la]->id,
                        'nilai' => Arr_random(Arr_pluck($getKegiatan[2]['nilai'], 'code'))
                    ];
                }
                $idPresensi++;
            }
        }

        PresensiGroup::insert($newPresGroup);
        foreach (array_chunk($newPresensi, 10000) as $setPresensi) Presensi::insert($setPresensi);
    }
}
