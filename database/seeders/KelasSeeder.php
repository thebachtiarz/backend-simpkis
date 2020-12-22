<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School\Curriculum\Kelas;
use App\Models\School\Curriculum\KelasGroup;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $newKelas = [];
        $kelasGroup = KelasGroup::get();
        for ($i = 0; $i < count($kelasGroup); $i++) {
            for ($j = 1; $j <= 3; $j++) {
                $newKelas[] = [
                    'nama' => $kelasGroup[$i]['nama_group'] . ' ' . $j,
                    'id_group' => $kelasGroup[$i]['id'],
                    'created_at' => Carbon_DBtimeNow(),
                    'updated_at' => Carbon_DBtimeNow()
                ];
            }
        }
        Kelas::insert($newKelas);
    }
}
