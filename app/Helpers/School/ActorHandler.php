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
    $getSiswa = Siswa::getSiswaNameByID($id_siswa);
    return $getSiswa->count() ? $getSiswa->get('nama')[0]['nama'] : '';
}
