<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School\Curriculum\KelasGroup;

class KelasGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $kelasGroup = [];
        $kelasName = ['Teknik Komputer Jaringan', 'Administrasi', 'Multimedia', 'Permesinan', 'Teknik Sepeda Motor', 'Keperawatan', 'Kesehatan'];
        for ($i = 0; $i < count($kelasName); $i++) {
            $kelasGroup[] = [
                'tingkat' => strval(Arr_random(['10', '11', '12'])),
                'nama_group' => $kelasName[$i],
                'status' => Cur_setKelasStatus('active'),
                'created_at' => Carbon_DBtimeNow(),
                'updated_at' => Carbon_DBtimeNow()
            ];
        }
        KelasGroup::insert($kelasGroup);
    }
}
