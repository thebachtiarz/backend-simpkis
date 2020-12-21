<?php

namespace App\Services\School\Curriculum;

use App\Models\School\Actor\Siswa;

class NilaiAkhirService
{
    protected static int $idSiswa;
    protected static int $idSemester;

    private static object $dataSiswa;
    private static array $dataPresensi;
    private static object $dataNilaiTambahan;
    private static array $dataKegiatan;

    private static int $stateNilaiPresensi = 0;
    private static int $stateNilaiTambahan = 0;
    private static array $benchOfNilaiAkhir;
    private static string $totalNilaiAkhir;
    private static string $stringNilaiAkhir;

    private static bool $status = false;
    private static array $errorMessage;

    // ? Public Method
    // generate for get result
    public static function generate()
    {
        self::runService();
        if (static::$status) {
            return self::getResult();
        } else {
            return self::$errorMessage;
        }
    }

    // ? Private Method
    // run process for get nilai akhir siswa
    private static function runService()
    {
        try {
            self::getSiswa();
            self::getPresensi();
            self::getNilaiTambahan();
            self::getKegiatan();
            self::getNilaiPresensiBenchmark();
            self::sumNilaiPresensi();
            self::sumNilaiTambahan();
            self::setTotalNilaiAkhir();
            self::setStringNilaiAkhir();
            self::$status = true;
        } catch (\Throwable $th) {
            self::$errorMessage = ['code' => $th->getCode(), 'message' => $th->getMessage()];
            self::$status = false;
        }
        self::resetIncrementValues();
    }

    // result of process is stored into array
    private static function getResult()
    {
        return [
            'id_siswa' => static::$idSiswa,
            'presensi' => strval(static::$stateNilaiPresensi),
            'tambahan' => strval(static::$stateNilaiTambahan),
            'totalNilai' => strval(static::$totalNilaiAkhir),
            'stringNilai' => static::$stringNilaiAkhir
        ];
    }

    // get siswa data by id siswa
    private static function getSiswa()
    {
        self::$dataSiswa = Siswa::find(static::$idSiswa);
    }

    // get presensi siswa by id siswa
    private static function getPresensi()
    {
        $presensi = static::$dataSiswa->presensi->where('id_semester', static::$idSemester);
        $data = [];
        foreach ($presensi as $key => $value) if ($value->presensigroup->approve == '7') $data[] = $value;
        self::$dataPresensi = $data;
    }

    // get nilai tambahan siswa by id siswa
    private static function getNilaiTambahan()
    {
        self::$dataNilaiTambahan = static::$dataSiswa->nilaitambahan->where('id_semester', static::$idSemester);
    }

    // get kegiatan data for get matched nilai presensi
    private static function getKegiatan()
    {
        self::$dataKegiatan = Atv_getKegiatanResource();
    }

    // ! Process core
    // sum nilai presensi
    private static function sumNilaiPresensi()
    {
        if (count(static::$dataPresensi))
            for ($i = 0; $i < count(static::$dataPresensi); $i++)
                self::$stateNilaiPresensi += in_array(static::$dataPresensi[$i]->nilai, array_keys(static::$dataKegiatan[static::$dataPresensi[$i]->presensigroup->id_kegiatan])) ?
                    static::$dataKegiatan[static::$dataPresensi[$i]->presensigroup->id_kegiatan][static::$dataPresensi[$i]->nilai] : 0;
    }

    // sum nilai tambahan
    private static function sumNilaiTambahan()
    {
        if (count(static::$dataNilaiTambahan))
            for ($i = 0; $i < count(static::$dataNilaiTambahan); $i++)
                self::$stateNilaiTambahan += in_array(static::$dataNilaiTambahan[$i]->nilai, array_keys(static::$dataKegiatan[static::$dataNilaiTambahan[$i]->id_kegiatan])) ?
                    static::$dataKegiatan[static::$dataNilaiTambahan[$i]->id_kegiatan][static::$dataNilaiTambahan[$i]->nilai] : 0;
    }

    // create the nilai akhir category by average nilai presensi (stored from kegiatan->nilai_avg) based from this siswa
    // every siswa can be different based from his count of presensi and nilai tambahan data
    private static function getNilaiPresensiBenchmark()
    {
        $resPresensiAvgNilai = Atv_getPresensiAvgNilai();
        $dataPresensiStaticAvg = 0;
        $resPresensiStaticString = ['D', 'D+', 'C', 'C+', 'B', 'B+', 'A'];
        $dataPresensiStaticString = [];
        //
        foreach ($resPresensiAvgNilai as $key => $value) {
            $dataPresensiStaticAvg += ($value['avg'] * count(collect(static::$dataPresensi)->where('presensigroup.id_kegiatan', $value['id'])));
        }
        $seventyPercentPresensiAvg = ($dataPresensiStaticAvg * 0.7);
        //
        for ($i = 1; $i <= count($resPresensiStaticString); $i++) {
            $dataPresensiStaticString[] = [
                $resPresensiStaticString[$i - 1] => strval(round(($seventyPercentPresensiAvg / count($resPresensiStaticString) * $i), 2))
            ];
        }
        self::$benchOfNilaiAkhir = Arr_collapse($dataPresensiStaticString);
    }

    // create nilai akhir detail in float
    private static function setTotalNilaiAkhir()
    {
        $process = (static::$stateNilaiPresensi * 0.7) + (static::$stateNilaiTambahan * 0.3);
        self::$totalNilaiAkhir = $process > 0 ? strval(round($process, 2)) : 0;
    }

    // create nilai akhir result into string
    private static function setStringNilaiAkhir()
    {
        $resultString = '';

        if (static::$totalNilaiAkhir == 0) $resultString = 'E';
        elseif (static::$totalNilaiAkhir < static::$benchOfNilaiAkhir['D']) $resultString = 'D';
        elseif (static::$totalNilaiAkhir < static::$benchOfNilaiAkhir['D+']) $resultString = 'D+';
        elseif (static::$totalNilaiAkhir < static::$benchOfNilaiAkhir['C']) $resultString = 'C';
        elseif (static::$totalNilaiAkhir < static::$benchOfNilaiAkhir['C+']) $resultString = 'C+';
        elseif (static::$totalNilaiAkhir < static::$benchOfNilaiAkhir['B']) $resultString = 'B';
        elseif (static::$totalNilaiAkhir < static::$benchOfNilaiAkhir['B+']) $resultString = 'B+';
        else $resultString = 'A';

        self::$stringNilaiAkhir = $resultString;
    }

    // ?! Reset attributes with having increment value for process
    // running at last process
    private static function resetIncrementValues()
    {
        self::$stateNilaiPresensi = 0;
        self::$stateNilaiTambahan = 0;
    }

    // ? Setter Module

    /**
     * Set the value of idSiswa
     *
     * @return  self
     */
    public static function setIdSiswa($idSiswa)
    {
        self::$idSiswa = $idSiswa;

        return new self;
    }

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
