<?php

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
        $kelasName = ['Teknik Komputer Jaringan', 'Administrasi', 'Multimedia'];
        $j = 0;
        for ($i = 10; $i <= 12; $i++) {
            $kelasGroup[] = [
                'tingkat' => strval($i),
                'nama_group' => $kelasName[$j]
            ];
            $j++;
        }
        KelasGroup::insert($kelasGroup);
    }
}
