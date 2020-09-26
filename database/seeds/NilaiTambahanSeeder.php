<?php

use App\Models\School\Activity\Kegiatan;
use Illuminate\Database\Seeder;
use App\Models\School\Activity\NilaiTambahan;
use App\Models\School\Actor\Siswa;
use App\Models\School\Curriculum\Semester;

class NilaiTambahanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $newNilaiTambahan = [];
        $getKegiatan = Kegiatan::getKegiatanTambahan()->get()->map->kegiatanCollectMap();
        $getIdSiswa = Arr_pluck(Siswa::get('id'), 'id');
        $getIdKegiatan = Arr_pluck($getKegiatan, 'id');
        for ($i = 0; $i < 4000; $i++) {
            $randKegiatan = (int) Arr_random($getIdKegiatan);
            $newNilaiTambahan[] = [
                'id_semester' => Cur_getActiveIDSemesterNow(),
                'id_siswa' => Arr_random($getIdSiswa),
                'id_kegiatan' => $randKegiatan,
                // if an error in array_search below, just ignore it, it's happen because dumb php-intelephense when debugging
                'nilai' => Arr_random(Arr_pluck($getKegiatan[array_search($randKegiatan, $getIdKegiatan)]['nilai'], 'code'))
            ];
        }
        NilaiTambahan::insert($newNilaiTambahan);
    }
}
