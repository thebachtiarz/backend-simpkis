<?php

namespace App\Services\School\Curriculum;

use App\Models\School\Curriculum\Kelas;
use App\Models\School\Curriculum\NilaiAkhirGroup;
use App\Models\School\Curriculum\NilaiAkhir;
use App\Services\School\Curriculum\NilaiAkhirService;
use ReflectionClass;

class NilaiAkhirCreatorService
{
    protected static int $idSemester;

    private static int $newNilaiAkhirGroupId;
    private static object $kelasData;
    private static array $finalNilaiAkhirGroup = [];
    private static array $finalNilaiAkhir = [];
    private static array $responseResult;
    private static bool $wasUpdated = false;

    // ? Public Method
    public static function runProcessNilaiAkhir()
    {
        self::$idSemester = Cur_getActiveIDSemesterNow();
        self::runService();
        return static::$responseResult;
    }

    // ? Private Method
    private static function runService()
    {
        Atv_cacheFlush();
        self::getNewNilaiAkhirId();
        self::getActiveKelas();
        self::deleteNilaiAkhirSmtNowIfAny();
        self::processCountingNilaiAkhir();
        self::saveResultNilaiAkhir();
        Atv_cacheFlush();
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
        self::$newNilaiAkhirGroupId = (int) (NilaiAkhirGroup::count() ? NilaiAkhirGroup::getLastNilaiAkhir()->first('id')->id : 0) + 1;
    }

    private static function getActiveKelas()
    {
        self::$kelasData = Kelas::getActiveKelas();
    }

    // ?! Process Core
    private static function processCountingNilaiAkhir()
    {
        for ($i = 0; $i < self::$kelasData->count(); $i++) {
            $setKelas = self::$kelasData->get()[$i];
            self::$finalNilaiAkhirGroup[] = [
                'id_semester' => static::$idSemester,
                'id_kelas' => $setKelas->id,
                'catatan' => 'Presensi Semester: ' . Cur_getSemesterNameByID(static::$idSemester) . ', Kelas: ' . Cur_getKelasNameByID($setKelas->id) . ', Tanggal: ' . Carbon_HumanFullDateTimeNow(),
                'created_at' => Carbon_DBtimeNow(),
                'updated_at' => Carbon_DBtimeNow()
            ];
            for ($j = 0; $j < count($setKelas->siswa); $j++) {
                $setSiswa = $setKelas->siswa[$j];
                $getNilai = NilaiAkhirService::setIdSiswa($setSiswa->id)->setIdSemester(static::$idSemester)->generate();
                self::$finalNilaiAkhir[] = [
                    'id_nilai' => static::$newNilaiAkhirGroupId,
                    'id_semester' => static::$idSemester,
                    'id_siswa' => $setSiswa->id,
                    'nilai_akhir' => Cur_setFormatNilaiAkhir($getNilai['totalNilai'], $getNilai['stringNilai']),
                    'created_at' => Carbon_DBtimeNow(),
                    'updated_at' => Carbon_DBtimeNow()
                ];
            }
            self::$newNilaiAkhirGroupId++;
        }
    }

    // ?! Saving into database
    private static function saveResultNilaiAkhir()
    {
        try {
            if ((!count(static::$finalNilaiAkhirGroup)) || (!count(static::$finalNilaiAkhir)))
                throw new \Exception('Tidak ada nilai akhir yang diproses', 404);
            foreach (array_chunk(static::$finalNilaiAkhirGroup, 10000) as $partNilaiAkhirGroup)
                NilaiAkhirGroup::insert($partNilaiAkhirGroup);
            foreach (array_chunk(static::$finalNilaiAkhir, 10000) as $partNilaiAkhir)
                NilaiAkhir::insert($partNilaiAkhir);
            self::$responseResult = successResponse('Berhasil ' . (static::$wasUpdated ? 'memperbarui' : 'memproses') . ' nilai akhir');
        } catch (\Throwable $th) {
            self::$responseResult = dataResponse(['code' => $th->getCode(), 'message' => $th->getMessage()], 'error', 'Gagal memproses nilai akhir');
        }
    }

    // ? Setter Module
    /**
     * Set the value of idSemester
     *
     * @return  self
     */
    public static function setIdSemester($idSemester)
    {
        self::$idSemester = $idSemester;

        return new self;
    }
}
