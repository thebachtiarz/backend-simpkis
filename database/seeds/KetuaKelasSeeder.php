<?php

use Illuminate\Database\Seeder;
use App\Models\School\Actor\KetuaKelas;
use App\Models\School\Actor\Siswa;
use App\Models\School\Curriculum\Kelas;

class KetuaKelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $newKetua = [];
        $getKelas = pluckArray(Kelas::all(), 'id');
        $getSiswa = pluckArray(Siswa::all(), 'id');
        for ($i = 0; $i < count($getKelas); $i++) {
            $newKetua[] = [
                'id_siswa' => strval(randArray($getSiswa)),
                'id_kelas' => strval($getKelas[$i])
            ];
        }
        KetuaKelas::insert($newKetua);
    }
}
