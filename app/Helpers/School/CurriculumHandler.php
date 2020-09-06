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
 * ! set format nilai akhir
 * for DB processing
 *
 * @param float $totalNilai
 * @param string $stringNilai
 * @return void
 */
function Cur_setFormatNilaiAkhir($totalNilai, $stringNilai)
{
    return serialize(['total' => $totalNilai, 'string' => $stringNilai]);
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
 * set format for kelas lulus by today
 *
 * @return void
 */
function Cur_formatKelasLulus()
{
    return '(L-' . Carbon_AnyDateParse(Carbon_DBdatetimeToday()) . ')';
}

/**
 * set status kelas
 *
 * @param string $status
 * @return void
 */
function Cur_setKelasStatus($status)
{
    if ($status == 'active') {
        return '7';
    } elseif ($status == 'graduated') {
        return '5';
    }
}

/**
 * get status kelas
 *
 * @param numeric $status
 * @return void
 */
function Cur_getKelasStatus($status)
{
    if ($status == '7') {
        return 'active';
    } elseif ($status == '5') {
        return 'graduated';
    }
}
