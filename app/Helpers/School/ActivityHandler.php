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
 * ! get repository kegiatan all
 * for DB processing
 *
 * @return void
 */
function Atv_getRepoKegiatanAll()
{
    return cache()->remember('repo-kegiatan-all', (60 * 60 * 2/* 2 hours */), function () {
        return Kegiatan::all()->map->kegiatanResourceMap();
    });
}

/**
 * ! get repository kegiatan presensi
 * for DB processing
 *
 * @return void
 */
function Atv_getRepoKegiatanPresensi()
{
    return cache()->remember('repo-kegiatan-presensi', (60 * 60 * 2/* 2 hours */), function () {
        return Kegiatan::getKegiatanPresensi()->get()->map->kegiatanResourceMap();
    });
}

/**
 * ! get repository kegiatan tambahan
 * for DB processing
 *
 * @return void
 */
function Atv_getRepoKegiatanTambahan()
{
    return cache()->remember('repo-kegiatan-tambahan', (60 * 60 * 2/* 2 hours */), function () {
        return Kegiatan::getKegiatanTambahan()->get()->map->kegiatanResourceMap();
    });
}

/**
 * ! get resources data kegiatan all
 * for DB processing
 *
 * @return void
 */
function Atv_getKegiatanResource()
{
    $data = Atv_getRepoKegiatanAll();
    $result = [];
    foreach ($data as $key => $value) $result[$value['id']] = $value['nilai'];
    return cache()->remember('res-kegiatan', (60 * 60 * 2/* 2 hours */), function () use ($result) {
        return $result;
    });
}

/**
 * ! get resources data kegiatan presensi
 * for DB processing
 *
 * @return void
 */
function Atv_getKegiatanPresensiResource()
{
    $data = Atv_getRepoKegiatanPresensi();
    $result = [];
    foreach ($data as $key => $value) $result[$value['id']] = $value['nilai'];
    return cache()->remember('res-kegiatan-presensi', (60 * 60 * 2/* 2 hours */), function () use ($result) {
        return $result;
    });
}

/**
 * ! get resources data kegiatan tambahan
 * for DB processing
 *
 * @return void
 */
function Atv_getKegiatanTambahanResource()
{
    $data = Atv_getRepoKegiatanTambahan();
    $result = [];
    foreach ($data as $key => $value) $result[$value['id']] = $value['nilai'];
    return cache()->remember('res-kegiatan-tambahan', (60 * 60 * 2/* 2 hours */), function () use ($result) {
        return $result;
    });
}

/**
 * ! get resources kegiatan presensi average nilai
 * for DB processing
 *
 * @return void
 */
function Atv_getPresensiAvgNilai()
{
    $resPresensi = Atv_getRepoKegiatanPresensi();
    $arrPresensiNilai = [];
    for ($i = 0; $i < count($resPresensi); $i++) {
        $arrPresensiNilai[] = [
            'id' => $resPresensi[$i]['id'],
            'avg' => $resPresensi[$i]['nilai_avg']
        ];
    }
    return cache()->remember('res-kegiatan-presensi-avg-nilai', (60 * 60 * 2/* 2 hours */), function () use ($arrPresensiNilai) {
        return $arrPresensiNilai;
    });
}

/**
 * ! get resources data presensi
 * for DB processing
 *
 * @param string $id_semester
 * @return void
 */
function Atv_getPresensiResource($id_semester)
{
    $data = Presensi::getPresensiResource($id_semester)->get()->map->presensiResourceMap();
    $result = [];
    foreach ($data as $key => $value) $result[$value['id_siswa']][] = $value;
    return cache()->remember('res-presensi', (60 * 60 * 2/* 2 hours */), function () use ($result) {
        return $result;
    });
}

/**
 * ! get resources data nilai tambahan
 * for DB processing
 *
 * @param string $id_semester
 * @return void
 */
function Atv_getNilaiTambahanResource($id_semester)
{
    $data = NilaiTambahan::getNilaiTambahanResource($id_semester)->get()->map->nilaitambahanResourceMap();
    $result = [];
    foreach ($data as $key => $value) $result[$value['id_siswa']][] = $value;
    return cache()->remember('res-nilaitambahan', (60 * 60 * 2/* 2 hours */), function () use ($result) {
        return $result;
    });
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
    // jika 'all' maka hanya boleh hari senin sampai jumat, atau hari yang ditentukan diluar itu
    if ((($day == Atv_setDayKegiatan('all')) && (Carbon_IsWorkDayNow())) || ($day == Carbon_DBDayNumOfWeek()))
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
 * get akses kegiatan type
 * for Human
 *
 * @param numeric $akses
 * @return void
 */
function Atv_HumanAksesKegiatan($akses)
{
    if ($akses == '5') return 'Presensi';
    elseif ($akses == '7') return 'Tambahan';
}

/**
 * convert presence approve code to string
 *
 * @param string $approve
 * @return void
 */
function Atv_convApproveCodeToString($approve)
{
    return $approve == '7' ? 'Sudah' : 'Belum';
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
