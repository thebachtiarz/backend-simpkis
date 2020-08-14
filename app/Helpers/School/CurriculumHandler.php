<?php

/**
 * use libraries
 */

/**
 * use models
 */

use App\Models\School\Curriculum\Kelas;
use App\Models\School\Curriculum\Semester;

/** */

/**
 * !: processing nilai_akhir with formula
 *
 * @param int $total_presensi
 * @param int $total_nilaitambahan
 * @return void
 */
function Cur_formulaNilaiAkhir($total_presensi, $total_nilaitambahan)
{
    $presTotal = $total_presensi;
    $niltamTotal = $total_nilaitambahan;
    $process = ($presTotal * 0.7) + ($niltamTotal * 0.3);
    $result = $process > 0 ? $process : 0;
    // proses untuk pengkategorian hasil akhir nilai
    return strval(round($result, 2));
}

/**
 * get semester name by id semester
 *
 * @param string $id_semester
 * @return void
 */
function Cur_getSemesterNameByID($id_semester)
{
    $getSemester = Semester::find($id_semester);
    return (bool) $getSemester ? "{$getSemester->semester}" : 'Semester tidak ditemukan';
}

/**
 * get kelas name by id kelas
 *
 * @param string $id_kelas
 * @return void
 */
function Cur_getKelasNameByID($id_kelas)
{
    $getKelas = Kelas::find($id_kelas);
    return (bool) $getKelas ? "{$getKelas->kelasgroup->tingkat} - {$getKelas->nama}" : 'Kelas tidak ditemukan';
}

/**
 * create new semester format
 *
 * @param string $year
 * @param string $code_semester
 * @return void
 */
function Cur_setNewSemesterFormat($year = '', $code_semester = '')
{
    $tahun = isset($year) ? $year : date('Y');
    $semester = isset($code_semester) ? Cur_convSemesterByCode($code_semester) : Cur_getSemesterNow();
    if ((!isset($year)) && ($semester == Cur_convSemesterByCode('2'))) $tahun--;
    return "{$tahun}/$semester";
}

/**
 * convert semester code to string
 *
 * @param string $code_semester
 * @return void
 */
function Cur_convSemesterByCode($code_semester)
{
    if ($code_semester == '1') return 'Ganjil';
    if ($code_semester == '2') return 'Genap';
}

/**
 * generate semester string now
 *
 * @return void
 */
function Cur_getSemesterNow()
{
    $monthNow = date('n');
    return (($monthNow >= 1) && ($monthNow <= 7)) ? Cur_convSemesterByCode('2') : Cur_convSemesterByCode('1');
}

/**
 * get active semester now
 *
 * @return void
 */
function Cur_getActiveIDSemesterNow()
{
    return Semester::orderByDesc('id')->first('id')->id;
}

/**
 * convert approve code to string
 *
 * @param string $approve
 * @return void
 */
function Cur_convApproveCodeToString($approve)
{
    return $approve == '7' ? 'Ya' : 'Tidak';
}

/**
 * set format for kelas lulus by today
 *
 * @return void
 */
function Cur_formatKelasLulus()
{
    return '(L-' . Carbon_AnyDateParse(Carbon_DBdatetimeToday()) . ')';
}
