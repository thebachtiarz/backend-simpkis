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
