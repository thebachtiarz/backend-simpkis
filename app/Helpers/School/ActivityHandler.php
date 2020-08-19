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
 * ! check if filling in attendance is allowed
 *
 * @param string $day
 * @param time $time_start
 * @param time $time_end
 * @return void
 */
function Atv_boolPresensiTimeAllowed($day, $time_start, $time_end)
{
    $result = false;
    if (($day == Atv_setDayKegiatan('all')) || ($day == Carbon_DBDayNumOfWeek()))
        if ((Carbon_AnyTimeNow() >= $time_start) && (Carbon_AnyTimeNow() <= $time_end)) $result = true;
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

/**
 * convert to numeric day from request
 * ex: mon (use: shortEnglishDayOfWeek)
 *
 * @param string $day
 * @return void
 */
function Atv_setDayKegiatan($day)
{
    $result = '';
    if ($day == 'mon') $result = '1';
    elseif ($day == 'tue') $result = '2';
    elseif ($day == 'wed') $result = '3';
    elseif ($day == 'thu') $result = '4';
    elseif ($day == 'fri') $result = '5';
    elseif ($day == 'sat') $result = '6';
    elseif ($day == 'all') $result = '*';
    return $result;
}

/**
 * convert day available kegiatan
 *
 * @param numeric $day
 * @param boolean $locale
 * @return void
 */
function Atv_getInfoDayKegiatan($day, $locale = false)
{
    if ($day == '*') return $locale ? 'Setiap hari' : 'Every day';
    else return Carbon_HumanDayNameOfWeek($day, $locale);
}
