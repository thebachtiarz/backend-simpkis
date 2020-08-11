<?php

/**
 * use libraries
 */

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
