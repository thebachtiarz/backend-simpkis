<?php

use Illuminate\Database\Seeder;
use App\Models\School\Curriculum\Kelas;

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
        for ($i = 0; $i < 12; $i++) {
            $newKelas[] = [
                'tingkat_kelas' => randArray(['10', '11', '12']),
                'nama_kelas' => randArray([
                    'Teknik Komputer Jaringan 1',
                    'Teknik Komputer Jaringan 2',
                    'Teknik Komputer Jaringan 3',
                    'Administrasi 1',
                    'Administrasi 2',
                    'Administrasi 3',
                    'Multimedia 1',
                    'Multimedia 2',
                    'Multimedia 3'
                ])
            ];
        }
        Kelas::insert($newKelas);
    }
}
