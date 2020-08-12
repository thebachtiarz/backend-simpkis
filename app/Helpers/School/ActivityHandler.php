<?php

/**
 * use libraries
 */

use App\Models\School\Activity\PresensiGroup;

/**
 * use models
 */

/** */

/**
 * set akses kegiatan
 * for DB
 * note: if [5 => [Presensi Wajib Ibadah], 7 => [Nilai Tambahan]]
 *
 * @param string $akses
 * @return void
 */
function Atv_setAksesKegiatan($akses)
{
    if ($akses == 'presensi') return '5';
    elseif ($akses == 'tambahan') return '7';
}

/**
 * get last id presensi group
 *
 * @return void
 */
function Atv_getLastIdPresensi()
{
    return PresensiGroup::orderByDesc('id')->first('id')->id;
}
