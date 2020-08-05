<?php

/**
 * use libraries
 */

/**
 * use models
 */

use App\Models\School\Actor\KetuaKelas;
use App\Models\School\Actor\Siswa;

/** */

/**
 * get siswa name by id
 *
 * @param string $id_siswa
 * @return void
 */
function Act_getSiswaNameByID($id_siswa)
{
    $getSiswa = Siswa::find($id_siswa);
    return (bool) $getSiswa ? $getSiswa->nama : '';
}
