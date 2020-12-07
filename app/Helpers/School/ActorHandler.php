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
 * @return string
 */
function Act_getSiswaNameByID($id_siswa): string
{
    $getSiswa = Siswa::find($id_siswa);
    return (bool) $getSiswa ? $getSiswa->nama : '';
}

/**
 * set format new ketua kelas username
 *
 * @param string $username
 * @return string
 */
function Act_formatNewKetuaKelasUsername($username): string
{
    return "u{$username}";
}

/**
 * set format new ketua kelas password
 * auto hash(ed) by default
 *
 * @param string $password
 * @return string
 */
function Act_formatNewKetuaKelasPassword($password): string
{
    return Hash::make("p{$password}");
}
