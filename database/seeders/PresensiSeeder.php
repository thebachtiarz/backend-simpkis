<?php

namespace Database\Seeders;

use App\Models\School\Actor\KetuaKelas;
use App\Services\Factory\PresensiFactoryService;
use Illuminate\Database\Seeder;

class PresensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $keg01PerSmt = 80;
        // $keg02PerSmt = 20;
        // $keg03PerSmt = 100;
        $keg01PerSmt = 40;
        $keg02PerSmt = 10;
        $keg03PerSmt = 50;
        //
        $getKetua = KetuaKelas::all();
        //
        for ($i = 0; $i < count($getKetua); $i++) {
            // presensi kegiatan id 1
            for ($j = 0; $j < $keg01PerSmt; $j++) {
                PresensiFactoryService::setIdUser($getKetua[$i]->id_user)->setIdKelas($getKetua[$i]->id_kelas)->setIdKegiatan(1)->setApprove(Arr_random([true, false]))->create();
            }
            // presensi kegiatan id 2
            for ($k = 0; $k < $keg02PerSmt; $k++) {
                PresensiFactoryService::setIdUser($getKetua[$i]->id_user)->setIdKelas($getKetua[$i]->id_kelas)->setIdKegiatan(2)->setApprove(Arr_random([true, false]))->create();
            }
            // presensi kegiatan id 3
            for ($l = 0; $l < $keg03PerSmt; $l++) {
                PresensiFactoryService::setIdUser($getKetua[$i]->id_user)->setIdKelas($getKetua[$i]->id_kelas)->setIdKegiatan(3)->setApprove(Arr_random([true, false]))->create();
            }
        }
    }
}
