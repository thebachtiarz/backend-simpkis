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
