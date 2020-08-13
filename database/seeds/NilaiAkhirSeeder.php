<?php

use Illuminate\Database\Seeder;
use App\Models\School\Activity\Kegiatan;
use App\Models\School\Activity\NilaiTambahan;
use App\Models\School\Activity\Presensi;
use App\Models\School\Actor\Siswa;
use App\Models\School\Curriculum\NilaiAkhirGroup;
use App\Models\School\Curriculum\NilaiAkhir;

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
        $idKelas = 1; // ! required from request, replace it when in services
        $idSemester = Cur_getActiveIDSemesterNow(); // ! optional from request, replace it when in services
        // ! do ifelse below it for checking if nilai akhir was processed
        $newNilaiAkhirGroupId = (int) (NilaiAkhirGroup::query()->count() ? (NilaiAkhirGroup::orderByDesc('id')->first('id')->id) : 0) + 1;
        $getSiswa = Siswa::select(['id'])->where('id_kelas', $idKelas);
        //
        $resKegiatan = Atv_getKegiatanResource();
        $resPresensi = Atv_getPresensiResource($idSemester);
        $resNilaiTam = Atv_getNilaiTambahanResource($idSemester);
        //
        $newNilaiAkhir = [];
        //
        for ($i = 0; $i < $getSiswa->count(); $i++) {
            $idSiswa = $getSiswa->get()[$i]['id'];
            $dataPresensiSiswa = array_key_exists($idSiswa, $resPresensi) ? $resPresensi[$idSiswa] : [];
            $dataNilaiTamSiswa = array_key_exists($idSiswa, $resNilaiTam) ? $resNilaiTam[$idSiswa] : [];
            //
            $presensiState = 0;
            $nilaitamState = 0;
            //
            if (count($dataPresensiSiswa)) {
                for ($j = 0; $j < count($dataPresensiSiswa); $j++) {
                    $presensiState += $resKegiatan[$dataPresensiSiswa[$j]['id_kegiatan']][$dataPresensiSiswa[$j]['nilai']];
                }
            }
            if (count($dataNilaiTamSiswa)) {
                for ($k = 0; $k < count($dataNilaiTamSiswa); $k++) {
                    $nilaitamState += $resKegiatan[$dataNilaiTamSiswa[$k]['id_kegiatan']][$dataNilaiTamSiswa[$k]['nilai']];
                }
            }
            //
            $newNilaiAkhir[] = ['id_siswa' => strval($idSiswa), 'nilai_akhir' => Cur_formulaNilaiAkhir($presensiState, $nilaitamState)];
            //
        }
        //
        $setNilaiAkhirGroup = [
            'id_semester' => $idSemester,
            'id_kelas' => $idKelas,
            'catatan' => /* get from request, ortherwise -> */ 'Presensi Semester: ' . Cur_getSemesterNameByID($idSemester) . ', Kelas: ' . Cur_getKelasNameByID($idKelas) . ', Tanggal: ' . Carbon_HumanFullDateTimeNow()
        ];
        $setNilaiAkhir = [];
        foreach ($newNilaiAkhir as $key => $value) {
            $setNilaiAkhir[] = [
                'id_nilai' => $newNilaiAkhirGroupId,
                'id_semester' => $idSemester,
                'id_siswa' => $value['id_siswa'],
                'nilai_akhir' => $value['nilai_akhir']
            ];
        }
        //
        NilaiAkhirGroup::insert($setNilaiAkhirGroup);
        NilaiAkhir::insert($setNilaiAkhir);
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
    }
}
