<?php

/**
 * use libraries
 */

use Illuminate\Support\Facades\Hash;

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

/**
 * set format new user username
 *
 * @param string $username
 * @return void
 */
function Act_formatNewSiswaUsername($username)
{
    return "u{$username}";
}

/**
 * set format new user password
 * auto hash(ed) by default
 *
 * @param string $password
 * @return void
 */
function Act_formatNewSiswaPassword($password)
{
    return Hash::make("p{$password}");
}
