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
        // $keg01PerSmt = 80;
        // $keg02PerSmt = 20;
        // $keg03PerSmt = 100;
        $keg01PerSmt = 40;
        $keg02PerSmt = 10;
        $keg03PerSmt = 50;
        //
        $newPresGroup = [];
        $newPresensi = [];
        //
        $getKetua = \App\Models\School\Actor\KetuaKelas::all();
        $getKegiatan = \App\Models\School\Activity\Kegiatan::getKegiatanPresensi()->get()->map->kegiatanCollectMap();
        //

        $idPresensi = 1;
        for ($i = 0; $i < count($getKetua); $i++) {
            //
            // presensi kegiatan id 1
            for ($j = 0; $j < $keg01PerSmt; $j++) {
                $newPresGroup[] = [
                    'id_kegiatan' => '1', 'id_user' => $getKetua[$i]->id_user, 'catatan' => $faker->sentence(), 'approve' => Arr_random(['7', '7', '7', '7', '7', '7', '7', '7', '7', '7', '7', '7', '7', '7', '7', '7', '5']),
                    'created_at' => Carbon_DBtimeNow(),
                    'updated_at' => Carbon_DBtimeNow()
                ];
                for ($ja = 0; $ja < count($getKetua[$i]->kelas->siswa); $ja++) {
                    $newPresensi[] = [
                        'id_presensi' => $idPresensi,
                        'id_semester' => Cur_getActiveIDSemesterNow(),
                        'id_siswa' => $getKetua[$i]->kelas->siswa[$ja]->id,
                        'nilai' => Arr_random(Arr_pluck($getKegiatan[0]['nilai'], 'code')),
                        'created_at' => Carbon_DBtimeNow(),
                        'updated_at' => Carbon_DBtimeNow()
                    ];
                }
                $idPresensi++;
            }
            //
            // presensi kegiatan id 2
            for ($k = 0; $k < $keg02PerSmt; $k++) {
                $newPresGroup[] = [
                    'id_kegiatan' => '2', 'id_user' => $getKetua[$i]->id_user, 'catatan' => $faker->sentence(), 'approve' => Arr_random(['7', '7', '7', '7', '7', '7', '7', '7', '7', '7', '7', '7', '7', '7', '7', '7', '5']),
                    'created_at' => Carbon_DBtimeNow(),
                    'updated_at' => Carbon_DBtimeNow()
                ];
                for ($ka = 0; $ka < count($getKetua[$i]->kelas->siswa); $ka++) {
                    $newPresensi[] = [
                        'id_presensi' => $idPresensi,
                        'id_semester' => Cur_getActiveIDSemesterNow(),
                        'id_siswa' => $getKetua[$i]->kelas->siswa[$ka]->id,
                        'nilai' => Arr_random(Arr_pluck($getKegiatan[1]['nilai'], 'code')),
                        'created_at' => Carbon_DBtimeNow(),
                        'updated_at' => Carbon_DBtimeNow()
                    ];
                }
                $idPresensi++;
            }
            //
            // presensi kegiatan id 3
            for ($l = 0; $l < $keg03PerSmt; $l++) {
                $newPresGroup[] = [
                    'id_kegiatan' => '3', 'id_user' => $getKetua[$i]->id_user, 'catatan' => $faker->sentence(), 'approve' => Arr_random(['7', '7', '7', '7', '7', '7', '7', '7', '7', '7', '7', '7', '7', '7', '7', '7', '5']),
                    'created_at' => Carbon_DBtimeNow(),
                    'updated_at' => Carbon_DBtimeNow()
                ];
                for ($la = 0; $la < count($getKetua[$i]->kelas->siswa); $la++) {
                    $newPresensi[] = [
                        'id_presensi' => $idPresensi,
                        'id_semester' => Cur_getActiveIDSemesterNow(),
                        'id_siswa' => $getKetua[$i]->kelas->siswa[$la]->id,
                        'nilai' => Arr_random(Arr_pluck($getKegiatan[2]['nilai'], 'code')),
                        'created_at' => Carbon_DBtimeNow(),
                        'updated_at' => Carbon_DBtimeNow()
                    ];
                }
                $idPresensi++;
            }
        }

        foreach (array_chunk($newPresGroup, 10000) as $setPresGroup) PresensiGroup::insert($setPresGroup);
        foreach (array_chunk($newPresensi, 10000) as $setPresensi) Presensi::insert($setPresensi);
    }
}
