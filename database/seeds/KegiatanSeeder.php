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
                    Str_random(6) => ['name' => 'Imam', 'poin' => '8'],
                    Str_random(6) => ['name' => 'Muadzin', 'poin' => '6'],
                    Str_random(6) => ['name' => 'Hadir', 'poin' => '4'],
                    Str_random(6) => ['name' => 'Haid', 'poin' => '0'],
                    Str_random(6) => ['name' => 'Alpha', 'poin' => '-2']
                ]),
                'akses' => Atv_setAksesKegiatan('presensi')
            ],
            [
                'nama' => 'Sholat Jumat',
                'nilai' => serialize([
                    Str_random(6) => ['name' => 'Imam', 'poin' => '10'],
                    Str_random(6) => ['name' => 'Muadzin', 'poin' => '7'],
                    Str_random(6) => ['name' => 'Hadir', 'poin' => '4'],
                    Str_random(6) => ['name' => 'Alpha', 'poin' => '-4']
                ]),
                'akses' => Atv_setAksesKegiatan('presensi')
            ],
            [
                'nama' => 'Sholat Sunah',
                'nilai' => serialize([
                    Str_random(6) => ['name' => 'Imam', 'poin' => '6'],
                    Str_random(6) => ['name' => 'Muadzin', 'poin' => '4'],
                    Str_random(6) => ['name' => 'Hadir', 'poin' => '3'],
                    Str_random(6) => ['name' => 'Alpha', 'poin' => '-1']
                ]),
                'akses' => Atv_setAksesKegiatan('presensi')
            ],
            [
                'nama' => 'Membersihkan Musholla',
                'nilai' => serialize([
                    Str_random(6) => ['name' => 'Tinggi', 'poin' => '6'],
                    Str_random(6) => ['name' => 'Sedang', 'poin' => '4'],
                    Str_random(6) => ['name' => 'Rendah', 'poin' => '2']
                ]),
                'akses' => Atv_setAksesKegiatan('tambahan')
            ],
            [
                'nama' => 'Jumat Mengaji',
                'nilai' => serialize([
                    Str_random(6) => ['name' => 'Tinggi', 'poin' => '12'],
                    Str_random(6) => ['name' => 'Sedang', 'poin' => '10'],
                    Str_random(6) => ['name' => 'Rendah', 'poin' => '6']
                ]),
                'akses' => Atv_setAksesKegiatan('tambahan')
            ],
            [
                'nama' => 'Khotbah Jumat',
                'nilai' => serialize([
                    Str_random(6) => ['name' => 'Tinggi', 'poin' => '15'],
                    Str_random(6) => ['name' => 'Sedang', 'poin' => '12'],
                    Str_random(6) => ['name' => 'Rendah', 'poin' => '10']
                ]),
                'akses' => Atv_setAksesKegiatan('tambahan')
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
