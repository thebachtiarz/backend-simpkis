<?php

use Illuminate\Database\Seeder;
use App\Models\School\Curriculum\Kelas;
use App\Models\School\Curriculum\NilaiAkhirGroup;
use App\Models\School\Curriculum\NilaiAkhir;
use App\Services\School\Curriculum\NilaiAkhirService;

class NilaiAkhirSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * ! this function is using in services
         */
        $idSemester = Cur_getActiveIDSemesterNow(); // ! optional from request, replace it when in services
        $newNilaiAkhirGroupId = (int) (NilaiAkhirGroup::query()->count() ? (NilaiAkhirGroup::orderByDesc('id')->first('id')->id) : 0) + 1;
        //
        $getKelas = Kelas::getActiveKelas();
        $newNilaiAkhirGroup = [];
        $newNilaiAkhir = [];
        for ($i = 0; $i < $getKelas->count(); $i++) {
            $setKelas = $getKelas->get()[$i];
            $checkAvailableProcess = \App\Models\School\Curriculum\NilaiAkhirGroup::getAvailableNilaiAkhirGroup($idSemester, $setKelas->id);
            if (!$checkAvailableProcess->count()) {
                $newNilaiAkhirGroup[] = [
                    'id_semester' => $idSemester,
                    'id_kelas' => $setKelas->id,
                    'catatan' => /* get from request, ortherwise -> */ 'Presensi Semester: ' . Cur_getSemesterNameByID($idSemester) . ', Kelas: ' . Cur_getKelasNameByID($setKelas->id) . ', Tanggal: ' . Carbon_HumanFullDateTimeNow()
                ];
                for ($j = 0; $j < count($setKelas->siswa); $j++) {
                    $setSiswa = $setKelas->siswa[$j];
                    $getNilai = (new NilaiAkhirService($setSiswa->id, $idSemester))->generate();
                    $newNilaiAkhir[] = [
                        'id_nilai' => $newNilaiAkhirGroupId,
                        'id_semester' => $idSemester,
                        'id_siswa' => $setSiswa->id,
                        'nilai_akhir' => Cur_setFormatNilaiAkhir($getNilai['totalNilai'], $getNilai['stringNilai'])
                    ];
                }
                $newNilaiAkhirGroupId++;
            }
        }
        //
        foreach (array_chunk($newNilaiAkhirGroup, 10000) as $partNilaiAkhirGroup) {
            NilaiAkhirGroup::insert($partNilaiAkhirGroup);
        }
        foreach (array_chunk($newNilaiAkhir, 10000) as $partNilaiAkhir) {
            NilaiAkhir::insert($partNilaiAkhir);
        }
        /**
         * 10   *   15  150
         * 8    *   12  96
         * 6    *   19  114
         * -4   *   12  -48
         *          58
         * 5.38
         *
         *
         */

        /**
         * keg_id 1 -> avg -> 4 -> /w 4 -> /s = 5mo -> 80      -> 320
         * keg_id 2 -> avg -> 4 -> /w 1 -> /s = 5mo -> 20      -> 80
         * keg_id 3 -> avg -> 3 -> /w 5 -> /s = 5mo -> 100     -> 300
         *
         *             avg/w -> 35 -> /s = 5mo -> 700
         *                                 70% -> 490
         *
         *             490/7 = 70
         *
         * 0 <  70 < 140 < 210 < 280 < 350 < 420 < 490
         * D    D+   C     C+    B     B+    A
         *
         *
         *
         */
    }
}
