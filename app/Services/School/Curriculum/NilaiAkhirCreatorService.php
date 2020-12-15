<?php

namespace App\Services\School\Curriculum;

use App\Models\School\Curriculum\Kelas;
use App\Models\School\Curriculum\NilaiAkhirGroup;
use App\Models\School\Curriculum\NilaiAkhir;
use App\Services\School\Curriculum\NilaiAkhirService;

class NilaiAkhirCreatorService
{
    protected static $id_semester;
    protected static $newNilaiAkhirGroupId;
    protected static $kelas;
    protected static $finalNilaiAkhirGroup = [];
    protected static $finalNilaiAkhir = [];
    protected static $responseResult;
    protected static $wasUpdated = false;

    // public
    public static function runProcessNilaiAkhir()
    {
        self::$id_semester = Cur_getActiveIDSemesterNow();
        self::runService();
        return self::$responseResult;
    }

    // private
    private static function runService()
    {
        self::$newNilaiAkhirGroupId = self::getNewNilaiAkhirId();
        self::$kelas = self::getActiveKelas();
        $delete = self::deleteNilaiAkhirSmtNowIfAny();
        self::processCountingNilaiAkhir();
        self::saveResultNilaiAkhir();
    }

    private static function deleteNilaiAkhirSmtNowIfAny()
    {
        $NilaiAkhirGroup = NilaiAkhirGroup::getBySemesterNow();
        if ($NilaiAkhirGroup->count()) {
            $nagID = [];
            foreach ($NilaiAkhirGroup->get('id') as $key => $value) $nagID[] = $value->id;
            $NilaiAkhirGroup->delete();
            NilaiAkhir::whereIn('id_nilai', $nagID)->delete();
            self::$wasUpdated = true;
        }
    }

    private static function getNewNilaiAkhirId()
    {
        return (int) (NilaiAkhirGroup::count() ? NilaiAkhirGroup::getLastNilaiAkhir()->first('id')->id : 0) + 1;
    }

    private static function getActiveKelas()
    {
        return Kelas::getActiveKelas();
    }

    private static function processCountingNilaiAkhir()
    {
        for ($i = 0; $i < self::$kelas->count(); $i++) {
            $setKelas = self::$kelas->get()[$i];
            self::$finalNilaiAkhirGroup[] = [
                'id_semester' => self::$id_semester,
                'id_kelas' => $setKelas->id,
                'catatan' => 'Presensi Semester: ' . Cur_getSemesterNameByID(self::$id_semester) . ', Kelas: ' . Cur_getKelasNameByID($setKelas->id) . ', Tanggal: ' . Carbon_HumanFullDateTimeNow(),
                'created_at' => Carbon_DBtimeNow(),
                'updated_at' => Carbon_DBtimeNow()
            ];
            for ($j = 0; $j < count($setKelas->siswa); $j++) {
                $setSiswa = $setKelas->siswa[$j];
                $getNilai = (new NilaiAkhirService)->setIdSiswa($setSiswa->id)->setIdSemester(self::$id_semester)->generate();
                self::$finalNilaiAkhir[] = [
                    'id_nilai' => self::$newNilaiAkhirGroupId,
                    'id_semester' => self::$id_semester,
                    'id_siswa' => $setSiswa->id,
                    'nilai_akhir' => Cur_setFormatNilaiAkhir($getNilai['totalNilai'], $getNilai['stringNilai']),
                    'created_at' => Carbon_DBtimeNow(),
                    'updated_at' => Carbon_DBtimeNow()
                ];
            }
            self::$newNilaiAkhirGroupId++;
        }
    }

    private static function saveResultNilaiAkhir()
    {
        try {
            if ((!count(self::$finalNilaiAkhirGroup)) || (!count(self::$finalNilaiAkhir)))
                throw new \Exception('Tidak ada nilai akhir yang diproses', 404);
            foreach (array_chunk(self::$finalNilaiAkhirGroup, 10000) as $partNilaiAkhirGroup)
                NilaiAkhirGroup::insert($partNilaiAkhirGroup);
            foreach (array_chunk(self::$finalNilaiAkhir, 10000) as $partNilaiAkhir)
                NilaiAkhir::insert($partNilaiAkhir);
            self::$responseResult = successResponse('Berhasil ' . (self::$wasUpdated ? 'memperbarui' : 'memproses') . ' nilai akhir');
        } catch (\Throwable $th) {
            self::$responseResult = dataResponse(['code' => $th->getCode(), 'message' => $th->getMessage()], 'error', 'Gagal memproses nilai akhir');
        }
    }
}
