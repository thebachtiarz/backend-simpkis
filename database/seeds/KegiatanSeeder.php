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
                'nama' => 'Sholat Dzuhur',
                'nilai' => serialize([
                    Str_random(6) => ['name' => 'Imam', 'poin' => '8'],
                    Str_random(6) => ['name' => 'Muadzin', 'poin' => '6'],
                    Str_random(6) => ['name' => 'Hadir', 'poin' => '4'],
                    Str_random(6) => ['name' => 'Haid', 'poin' => '0'],
                    Str_random(6) => ['name' => 'Alpha', 'poin' => '-2']
                ]),
                'nilai_avg' => 5,
                'hari' => Atv_setDayKegiatan('all'),
                'waktu_mulai' => Carbon_AnyTimeParse('11:30'),
                'waktu_selesai' => Carbon_AnyTimeParse('13:00'),
                'akses' => Atv_setAksesKegiatan('presensi'),
                'created_at' => Carbon_DBtimeNow(),
                'updated_at' => Carbon_DBtimeNow()
            ],
            [
                'nama' => 'Sholat Jumat',
                'nilai' => serialize([
                    Str_random(6) => ['name' => 'Imam', 'poin' => '10'],
                    Str_random(6) => ['name' => 'Muadzin', 'poin' => '7'],
                    Str_random(6) => ['name' => 'Hadir', 'poin' => '4'],
                    Str_random(6) => ['name' => 'Alpha', 'poin' => '-4']
                ]),
                'nilai_avg' => 4,
                'hari' => Atv_setDayKegiatan('fri'),
                'waktu_mulai' => Carbon_AnyTimeParse('11:30'),
                'waktu_selesai' => Carbon_AnyTimeParse('13:00'),
                'akses' => Atv_setAksesKegiatan('presensi'),
                'created_at' => Carbon_DBtimeNow(),
                'updated_at' => Carbon_DBtimeNow()
            ],
            [
                'nama' => 'Sholat Dhuha',
                'nilai' => serialize([
                    Str_random(6) => ['name' => 'Imam', 'poin' => '6'],
                    Str_random(6) => ['name' => 'Muadzin', 'poin' => '4'],
                    Str_random(6) => ['name' => 'Hadir', 'poin' => '3'],
                    Str_random(6) => ['name' => 'Alpha', 'poin' => '-1']
                ]),
                'nilai_avg' => 3,
                'hari' => Atv_setDayKegiatan('all'),
                'waktu_mulai' => Carbon_AnyTimeParse('09:00'),
                'waktu_selesai' => Carbon_AnyTimeParse('10:30'),
                'akses' => Atv_setAksesKegiatan('presensi'),
                'created_at' => Carbon_DBtimeNow(),
                'updated_at' => Carbon_DBtimeNow()
            ],
            [
                'nama' => 'Membersihkan Musholla',
                'nilai' => serialize([
                    Str_random(6) => ['name' => 'Tinggi', 'poin' => '6'],
                    Str_random(6) => ['name' => 'Sedang', 'poin' => '4'],
                    Str_random(6) => ['name' => 'Rendah', 'poin' => '2']
                ]),
                'nilai_avg' => 0,
                'hari' => Atv_setDayKegiatan('all'),
                'waktu_mulai' => Carbon_AnyTimeParse(),
                'waktu_selesai' => Carbon_AnyTimeParse(),
                'akses' => Atv_setAksesKegiatan('tambahan'),
                'created_at' => Carbon_DBtimeNow(),
                'updated_at' => Carbon_DBtimeNow()
            ],
            [
                'nama' => 'Jumat Mengaji',
                'nilai' => serialize([
                    Str_random(6) => ['name' => 'Tinggi', 'poin' => '12'],
                    Str_random(6) => ['name' => 'Sedang', 'poin' => '10'],
                    Str_random(6) => ['name' => 'Rendah', 'poin' => '6']
                ]),
                'nilai_avg' => 0,
                'hari' => Atv_setDayKegiatan('fri'),
                'waktu_mulai' => Carbon_AnyTimeParse('11:00'),
                'waktu_selesai' => Carbon_AnyTimeParse('13:00'),
                'akses' => Atv_setAksesKegiatan('tambahan'),
                'created_at' => Carbon_DBtimeNow(),
                'updated_at' => Carbon_DBtimeNow()
            ],
            [
                'nama' => 'Khotbah Jumat',
                'nilai' => serialize([
                    Str_random(6) => ['name' => 'Tinggi', 'poin' => '15'],
                    Str_random(6) => ['name' => 'Sedang', 'poin' => '12'],
                    Str_random(6) => ['name' => 'Rendah', 'poin' => '10']
                ]),
                'nilai_avg' => 0,
                'hari' => Atv_setDayKegiatan('fri'),
                'waktu_mulai' => Carbon_AnyTimeParse('11:00'),
                'waktu_selesai' => Carbon_AnyTimeParse('13:00'),
                'akses' => Atv_setAksesKegiatan('tambahan'),
                'created_at' => Carbon_DBtimeNow(),
                'updated_at' => Carbon_DBtimeNow()
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
