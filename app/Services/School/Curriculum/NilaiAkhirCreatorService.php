<?php

namespace App\Services\School\Curriculum;

use App\Models\School\Curriculum\Kelas;
use App\Models\School\Curriculum\NilaiAkhirGroup;
use App\Models\School\Curriculum\NilaiAkhir;
use App\Services\School\Curriculum\NilaiAkhirService;

class NilaiAkhirCreatorService
{
    protected $id_semester;
    protected $newNilaiAkhirGroupId;
    protected $kelas;
    protected $finalNilaiAkhirGroup = [];
    protected $finalNilaiAkhir = [];
    protected $responseResult;

    public function __construct($id_semester)
    {
        $this->id_semester = $id_semester;
        $this->newNilaiAkhirGroupId = $this->getNewNilaiAkhirId();
        $this->kelas = $this->getActiveKelas();
        $this->processCountingNilaiAkhir();
        $this->saveResultNilaiAkhir();
    }

    public function result(): array
    {
        return $this->responseResult;
    }

    private function getNewNilaiAkhirId()
    {
        return (int) (NilaiAkhirGroup::count() ? NilaiAkhirGroup::getLastNilaiAkhir()->first('id')->id : 0) + 1;
    }

    private function getActiveKelas()
    {
        return Kelas::getActiveKelas();
    }

    private function processCountingNilaiAkhir()
    {
        for ($i = 0; $i < $this->kelas->count(); $i++) {
            $setKelas = $this->kelas->get()[$i];
            $checkAvailableNilaiAkhirGroup = NilaiAkhirGroup::getAvailableNilaiAkhirGroup($this->id_semester, $setKelas->id);
            if (!$checkAvailableNilaiAkhirGroup->count()) {
                $this->finalNilaiAkhirGroup[] = [
                    'id_semester' => $this->id_semester,
                    'id_kelas' => $setKelas->id,
                    'catatan' => 'Presensi Semester: ' . Cur_getSemesterNameByID($this->id_semester) . ', Kelas: ' . Cur_getKelasNameByID($setKelas->id) . ', Tanggal: ' . Carbon_HumanFullDateTimeNow()
                ];
                for ($j = 0; $j < count($setKelas->siswa); $j++) {
                    $setSiswa = $setKelas->siswa[$j];
                    $getNilai = (new NilaiAkhirService($setSiswa->id, $this->id_semester))->generate();
                    $this->finalNilaiAkhir[] = [
                        'id_nilai' => $this->newNilaiAkhirGroupId,
                        'id_semester' => $this->id_semester,
                        'id_siswa' => $setSiswa->id,
                        'nilai_akhir' => Cur_setFormatNilaiAkhir($getNilai['totalNilai'], $getNilai['stringNilai'])
                    ];
                }
                $this->newNilaiAkhirGroupId++;
            }
        }
    }

    private function saveResultNilaiAkhir()
    {
        try {
            if ((!count($this->finalNilaiAkhirGroup)) || (!count($this->finalNilaiAkhir)))
                throw new \Exception('Tidak ada nilai akhir yang diproses', 2);
            foreach (array_chunk($this->finalNilaiAkhirGroup, 10000) as $partNilaiAkhirGroup)
                NilaiAkhirGroup::insert($partNilaiAkhirGroup);
            foreach (array_chunk($this->finalNilaiAkhir, 10000) as $partNilaiAkhir)
                NilaiAkhir::insert($partNilaiAkhir);
            $this->responseResult = successResponse('Berhasil memproses nilai akhir');
        } catch (\Throwable $th) {
            $this->responseResult = dataResponse(['error' => $th->getCode()], 'error', 'Gagal memproses nilai akhir');
        }
    }
}
