<?php

namespace App\Services\School\Curriculum;

use App\Models\School\Actor\Siswa;

class NilaiAkhirService
{
    protected $id_siswa, $id_semester;
    protected $data_siswa, $data_presensi, $data_nilaitambahan, $data_kegiatan;
    protected $stateNilaiPresensi, $stateNilaiTambahan = 0;
    protected $totalNilaiAkhir, $stringNilaiAkhir;

    public function __construct($id_siswa, $id_semester)
    {
        $this->id_siswa = $id_siswa;
        $this->id_semester = $id_semester;
        $this->data_siswa = $this->getSiswa($id_siswa);
        $this->data_presensi = $this->getPresensi();
        $this->data_nilaitambahan = $this->getNilaiTambahan();
        $this->data_kegiatan = $this->getKegiatan();
        $this->presensi_avg_nilai = $this->generateAverageNilai();
        $this->sumNilaiPresensi();
        $this->sumNilaiTambahan();
        $this->totalNilaiAkhir = $this->setTotalNilaiAkhir();
        $this->stringNilaiAkhir = $this->setStringNilaiAkhir();
    }

    public function generate()
    {
        return [
            'id_siswa' => $this->id_siswa,
            'presensi' => strval($this->stateNilaiPresensi),
            'tambahan' => strval($this->stateNilaiTambahan),
            'totalNilai' => strval($this->totalNilaiAkhir),
            'stringNilai' => $this->stringNilaiAkhir
        ];
    }

    # private
    private function getSiswa($id_siswa)
    {
        return Siswa::find($id_siswa);
    }

    private function getPresensi()
    {
        $presensi = $this->data_siswa->presensi->where('id_semester', $this->id_semester);
        $data = [];
        foreach ($presensi as $key => $value) if ($presensi[$key]->presensigroup->approve == '7') $data[] = $value;
        return $data;
    }

    private function getNilaiTambahan()
    {
        return $this->data_siswa->nilaitambahan->where('id_semester', $this->id_semester);
    }

    private function getKegiatan()
    {
        return Atv_getKegiatanResource();
    }

    private function sumNilaiPresensi()
    {
        if (count($this->data_presensi))
            for ($i = 0; $i < count($this->data_presensi); $i++)
                $this->stateNilaiPresensi += $this->data_kegiatan[$this->data_presensi[$i]->presensigroup->id_kegiatan][$this->data_presensi[$i]->nilai];
    }

    private function sumNilaiTambahan()
    {
        if (count($this->data_nilaitambahan))
            for ($i = 0; $i < count($this->data_nilaitambahan); $i++)
                $this->stateNilaiTambahan += $this->data_kegiatan[$this->data_nilaitambahan[$i]->id_kegiatan][$this->data_nilaitambahan[$i]->nilai];
    }

    private function setTotalNilaiAkhir()
    {
        $process = ($this->stateNilaiPresensi * 0.7) + ($this->stateNilaiTambahan * 0.3);
        $result = $process > 0 ? $process : 0;
        return strval(round($result, 2));
    }

    private function setStringNilaiAkhir()
    {
        if ($this->totalNilaiAkhir < $this->presensi_avg_nilai['D']) return 'D';
        elseif ($this->totalNilaiAkhir < $this->presensi_avg_nilai['D+']) return 'D+';
        elseif ($this->totalNilaiAkhir < $this->presensi_avg_nilai['C']) return 'C';
        elseif ($this->totalNilaiAkhir < $this->presensi_avg_nilai['C+']) return 'C+';
        elseif ($this->totalNilaiAkhir < $this->presensi_avg_nilai['B']) return 'B';
        elseif ($this->totalNilaiAkhir < $this->presensi_avg_nilai['B+']) return 'B+';
        else return 'A';
    }

    private function generateAverageNilai()
    {
        $resPresensiAvgNilai = Atv_getPresensiAvgNilai();
        $dataPresensiStaticAvg = 0;
        $resPresensiStaticString = ['D', 'D+', 'C', 'C+', 'B', 'B+', 'A'];
        $dataPresensiStaticString = [];
        //
        foreach ($resPresensiAvgNilai as $key => $value) {
            $dataPresensiStaticAvg += ($value['avg'] * count(collect($this->data_presensi)->where('presensigroup.id_kegiatan', $value['id'])));
        }
        $seventyPercentPresensiAvg = ($dataPresensiStaticAvg * 0.7);
        //
        for ($i = 1; $i <= count($resPresensiStaticString); $i++) {
            $dataPresensiStaticString[] = [
                $resPresensiStaticString[$i - 1] => strval(round(($seventyPercentPresensiAvg / count($resPresensiStaticString) * $i), 2))
            ];
        }
        return Arr_collapse($dataPresensiStaticString);
    }
}
