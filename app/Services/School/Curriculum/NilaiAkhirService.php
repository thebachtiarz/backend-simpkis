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
        foreach ($presensi as $key => $value) if ($value->presensigroup->approve == '7') $data[] = $value;
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
                $this->stateNilaiPresensi += in_array($this->data_presensi[$i]->nilai, array_keys($this->data_kegiatan[$this->data_presensi[$i]->presensigroup->id_kegiatan])) ?
                    $this->data_kegiatan[$this->data_presensi[$i]->presensigroup->id_kegiatan][$this->data_presensi[$i]->nilai] : 0;
    }

    private function sumNilaiTambahan()
    {
        if (count($this->data_nilaitambahan))
            for ($i = 0; $i < count($this->data_nilaitambahan); $i++)
                $this->stateNilaiTambahan += in_array($this->data_nilaitambahan[$i]->nilai, array_keys($this->data_kegiatan[$this->data_nilaitambahan[$i]->id_kegiatan])) ?
                    $this->data_kegiatan[$this->data_nilaitambahan[$i]->id_kegiatan][$this->data_nilaitambahan[$i]->nilai] : 0;
    }

    private function setTotalNilaiAkhir()
    {
        $process = ($this->stateNilaiPresensi * 0.7) + ($this->stateNilaiTambahan * 0.3);
        $result = $process > 0 ? $process : 0;
        return strval(round($result, 2));
    }

    private function setStringNilaiAkhir()
    {
        if ($this->totalNilaiAkhir == 0) return 'E';
        elseif ($this->totalNilaiAkhir < $this->presensi_avg_nilai['D']) return 'D';
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

/**
 * !! this is how this services work
 *
 * mengambil nilai rata-rata dari setiap kegiatan presensi
 * dikalikan dengan (berapa kali siswa melakukan presensi berdasarkan kegiatan) per kegiatan
 * (diasumsikan rata-rata presensi dilakukan per semester, jumlah presensi adalah 200 kali (80+20+100))
 * hasil dari nilai rata-rata tersebut kemudian dikalikan 70%
 * sehingga pada nilai rata-rata 70%, agar mendapatkan nilai A
 * (nilai 70% tersebut dibagi menjadi 7 kelompok (terdapat di bawah))
 * sehingga menghasilkan nilai akhir berdasarkan presensi selama satu semester
 *
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
 */
