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
        $getIdSemester = pluckArray(Semester::get('id'), 'id');
        $getIdSiswa = pluckArray(Siswa::get('id'), 'id');
        $getIdKegiatan = pluckArray($getKegiatan, 'id');
        for ($i = 0; $i < 4000; $i++) {
            $randKegiatan = (int) randArray($getIdKegiatan);
            $newNilaiTambahan[] = [
                'id_semester' => randArray($getIdSemester),
                'id_siswa' => randArray($getIdSiswa),
                'id_kegiatan' => $randKegiatan,
                // if an error in array_search below, just ignore it, it's happen because dumb php-intelephense when debugging
                'nilai' => randArray(pluckArray($getKegiatan[array_search($randKegiatan, $getIdKegiatan)]['nilai'], 'code'))
            ];
        }
        NilaiTambahan::insert($newNilaiTambahan);
    }
}
