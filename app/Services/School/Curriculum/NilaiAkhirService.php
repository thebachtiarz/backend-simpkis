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
        return $this->data_siswa->presensi->where('id_semester', $this->id_semester);
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
                $this->stateNilaiPresensi += $this->data_kegiatan[$this->data_presensi[$i]->id_kegiatan][$this->data_presensi[$i]->nilai];
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
        if ($this->totalNilaiAkhir < 70) return 'D';
        elseif ($this->totalNilaiAkhir < 140) return 'D+';
        elseif ($this->totalNilaiAkhir < 210) return 'C';
        elseif ($this->totalNilaiAkhir < 280) return 'C+';
        elseif ($this->totalNilaiAkhir < 350) return 'B';
        elseif ($this->totalNilaiAkhir < 420) return 'B+';
        else return 'A';
    }
}
