<?php

/**
 * use libraries
 */

/**
 * use models
 */

use App\Models\School\Curriculum\Kelas;

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
    return (($monthNow >= 1) && ($monthNow <= 7)) ? 'Genap' : 'Ganjil';
}
