<?php

namespace App\Services\Factory;

use App\Models\Auth\User;
use App\Models\School\Activity\Kegiatan;
use App\Models\School\Activity\PresensiGroup;
use App\Models\School\Actor\Siswa;
use App\Models\School\Curriculum\Kelas;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use ReflectionClass;

class PresensiFactoryService
{
    protected static int $idUser;
    protected static int $idSemester;
    protected static int $idKelas;
    protected static int $idKegiatan;
    protected static int $idSiswa;
    protected static bool $approve;

    private static ?object $userData;
    private static ?object $kelasData;
    private static ?object $siswaData;
    private static ?object $kegiatanData;

    private static array $presensiResult;
    private static array $errorMessage;
    private static bool $status = false;

    // ? Public Method
    public static function create()
    {
        self::createNewPresensi();
        if (static::$status) {
            return static::$presensiResult;
        } else {
            return static::$errorMessage;
        }
    }

    // ? Private Method
    private static function createNewPresensi()
    {
        self::getUser();
        $semester = self::checkPropVal('idSemester') ? static::$idSemester : Cur_getActiveIDSemesterNow();
        $isKelas = self::checkPropVal('idKelas');
        self::getKegiatan();
        $kegiatanNilai = static::$kegiatanData->kegiatanResourceMap();
        $faker = Faker::create();
        /**
         * jika kelas di sebutkan, maka presensi yang dilakukan adalah satu kelas tersebut
         * jika tidak, maka presensi secara spesifik berdasarkan id siswa
         */
        if ($isKelas) {
            self::getKelas();
            if (!!static::$kelasData) {
                try {
                    DB::beginTransaction();
                    $newPresensi = PresensiGroup::create([
                        'id_kegiatan' => static::$kegiatanData->id,
                        'id_user' => static::$userData->id,
                        'catatan' => $faker->sentence(),
                        'approve' => static::$approve ? '7' : '5'
                    ]);
                    $newPresensiSiswa = [];
                    foreach (static::$kelasData->siswa as $key => $siswa) {
                        $newPresensiSiswa[] = [
                            'id_presensi' => $newPresensi->id,
                            'id_semester' => $semester,
                            'id_siswa' => $siswa->id,
                            'nilai' => Arr_random(array_keys($kegiatanNilai['nilai']))
                        ];
                    }
                    $newPresensi->presensi()->createMany($newPresensiSiswa);
                    self::$presensiResult = dataResponse($newPresensi);
                    self::$status = true;
                    DB::commit();
                } catch (\Throwable $th) {
                    self::$errorMessage = errorResponse(['code' => $th->getCode(), 'message' => $th->getMessage()]);
                    self::$status = false;
                    DB::rollBack();
                }
            }
        } else {
            self::getSiswa();
            if (!!static::$siswaData) {
                try {
                    DB::beginTransaction();
                    $newPresensi = PresensiGroup::create([
                        'id_kegiatan' => static::$kegiatanData->id,
                        'id_user' => static::$userData->id,
                        'catatan' => $faker->sentence(),
                        'approve' => static::$approve ? '7' : '5'
                    ]);
                    $newPresensi->presensi()->createMany([
                        [
                            'id_presensi' => $newPresensi->id,
                            'id_semester' => $semester,
                            'id_siswa' => static::$siswaData->id,
                            'nilai' => Arr_random(array_keys($kegiatanNilai['nilai']))
                        ]
                    ]);
                    self::$presensiResult = dataResponse($newPresensi);
                    self::$status = true;
                    DB::commit();
                } catch (\Throwable $th) {
                    self::$errorMessage = errorResponse(['code' => $th->getCode(), 'message' => $th->getMessage()]);
                    self::$status = false;
                    DB::rollBack();
                }
            }
        }
    }

    // ? Private Method Core
    private static function getUser()
    {
        self::$userData = User::find(static::$idUser);
    }

    private static function getKelas()
    {
        self::$kelasData = Kelas::find(static::$idKelas);
    }

    private static function getSiswa()
    {
        self::$siswaData = Siswa::find(static::$idSiswa);
    }

    private static function getKegiatan()
    {
        self::$kegiatanData = Kegiatan::find(static::$idKegiatan);
    }

    // ?! check the property value is set
    private static function checkPropVal($prop)
    {
        return !!(new ReflectionClass(self::class))->getStaticPropertyValue($prop);
    }

    // ? Setter Module

    /**
     * Set the value of idUser
     *
     * @return  self
     */
    public static function setIdUser($idUser)
    {
        self::$idUser = $idUser;

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

    /**
     * Set the value of idKelas
     *
     * @return  self
     */
    public static function setIdKelas($idKelas)
    {
        self::$idKelas = $idKelas;

        return new self;
    }

    /**
     * Set the value of idKegiatan
     *
     * @return  self
     */
    public static function setIdKegiatan($idKegiatan)
    {
        self::$idKegiatan = $idKegiatan;

        return new self;
    }

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
     * Set the value of approve
     *
     * @return  self
     */
    public static function setApprove(bool $approve = true)
    {
        self::$approve = $approve;

        return new self;
    }
}
