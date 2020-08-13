<?php

/**
 * use libraries
 */

use App\Models\School\Activity\Kegiatan;
use App\Models\School\Activity\NilaiTambahan;
use App\Models\School\Activity\Presensi;
use App\Models\School\Activity\PresensiGroup;

/**
 * use models
 */

/** */

/**
 * ! get resources data kegiatan
 * for DB processing
 *
 * @return void
 */
function Atv_getKegiatanResource()
{
    $data = Kegiatan::all()->map->kegiatanResourceMap();
    $result = [];
    foreach ($data as $key => $value) $result[$value['id']] = $value['nilai'];
    return $result;
}

/**
 * ! get resources data presensi
 *
 * @param string $id_semester
 * @return void
 */
function Atv_getPresensiResource($id_semester)
{
    $data = Presensi::getPresensiResource($id_semester)->get()->map->presensiResourceMap();
    $result = [];
    foreach ($data as $key => $value) $result[$value['id_siswa']][] = $value;
    return $result;
}

/**
 * ! get resources data nilai tambahan
 *
 * @param string $id_semester
 * @return void
 */
function Atv_getNilaiTambahanResource($id_semester)
{
    $data = NilaiTambahan::getNilaiTambahanResource($id_semester)->get()->map->nilaitambahanResourceMap();
    $result = [];
    foreach ($data as $key => $value) $result[$value['id_siswa']][] = $value;
    return $result;
}

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
