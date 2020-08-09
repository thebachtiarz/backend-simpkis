<?php

use App\Models\School\Activity\Kegiatan;
use Illuminate\Database\Seeder;

class KegiatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $newKegiatan = [
            [
                'nama' => 'Sholat Wajib',
                'nilai' => serialize([
                    randString(6) => ['name' => 'imam', 'poin' => '8'],
                    randString(6) => ['name' => 'muadzin', 'poin' => '6'],
                    randString(6) => ['name' => 'hadir', 'poin' => '4'],
                    randString(6) => ['name' => 'haid', 'poin' => '0'],
                    randString(6) => ['name' => 'alpha', 'poin' => '-2']
                ]),
                'akses' => '7'
            ],
            [
                'nama' => 'Sholat Jumat',
                'nilai' => serialize([
                    randString(6) => ['name' => 'imam', 'poin' => '10'],
                    randString(6) => ['name' => 'muadzin', 'poin' => '7'],
                    randString(6) => ['name' => 'hadir', 'poin' => '4'],
                    randString(6) => ['name' => 'alpha', 'poin' => '-4']
                ]),
                'akses' => '7'
            ],
            [
                'nama' => 'Sholat Sunah',
                'nilai' => serialize([
                    randString(6) => ['name' => 'imam', 'poin' => '6'],
                    randString(6) => ['name' => 'muadzin', 'poin' => '4'],
                    randString(6) => ['name' => 'hadir', 'poin' => '3'],
                    randString(6) => ['name' => 'alpha', 'poin' => '-1']
                ]),
                'akses' => '7'
            ]
        ];
        Kegiatan::insert($newKegiatan);
    }
    // $data = [
    //     'a' => ['name' => 'imam', 'poin' => '8'],
    //     'b' => ['name' => 'muadzin', 'poin' => '6'],
    //     'c' => ['name' => 'hadir', 'poin' => '4'],
    //     'd' => ['name' => 'haid', 'poin' => '0'],
    //     'e' => ['name' => 'alpha', 'poin' => '-2']
    // ];
    // $nilai = ['c', 'd', 'b', 'a', 'a', 'e'];
    // $deploy = (int) '';
    // for ($i = 0; $i < count($nilai); $i++) {
    //     $deploy += (int) $data[$nilai[$i]]['poin'];
    // }
    // return $deploy;
}
