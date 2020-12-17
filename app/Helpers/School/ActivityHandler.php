<?php

/**
 * use libraries
 */

use Illuminate\Support\Facades\Cache;

/**
 * use models
 */

use App\Models\School\Activity\Kegiatan;
use App\Models\School\Activity\NilaiTambahan;
use App\Models\School\Activity\Presensi;
use App\Models\School\Activity\PresensiGroup;

/** */

/**
 * removing entire cache
 *
 * @return void
 */
function Atv_cacheFlush(): void
{
    Cache::flush();
}

/**
 * ! get repository kegiatan all
 * for DB processing
 *
 * @return object
 */
function Atv_getRepoKegiatanAll(): object
{
    return Cache::remember('repo-kegiatan-all', (60 * 60 * 2/* 2 hours */), function () {
        return Kegiatan::all()->map->kegiatanResourceMap();
    });
}

/**
 * ! get repository kegiatan presensi
 * for DB processing
 *
 * @return object
 */
function Atv_getRepoKegiatanPresensi(): object
{
    return Cache::remember('repo-kegiatan-presensi', (60 * 60 * 2/* 2 hours */), function () {
        return Kegiatan::getKegiatanPresensi()->get()->map->kegiatanResourceMap();
    });
}

/**
 * ! get repository kegiatan tambahan
 * for DB processing
 *
 * @return object
 */
function Atv_getRepoKegiatanTambahan(): object
{
    return Cache::remember('repo-kegiatan-tambahan', (60 * 60 * 2/* 2 hours */), function () {
        return Kegiatan::getKegiatanTambahan()->get()->map->kegiatanResourceMap();
    });
}

/**
 * ! get resources data kegiatan all
 * for DB processing
 *
 * @return array
 */
function Atv_getKegiatanResource(): array
{
    $data = Atv_getRepoKegiatanAll();
    $result = [];
    foreach ($data as $key => $value) $result[$value['id']] = $value['nilai'];
    return Cache::remember('res-kegiatan', (60 * 60 * 2/* 2 hours */), function () use ($result) {
        return $result;
    });
}

/**
 * ! get resources data kegiatan presensi
 * for DB processing
 *
 * @return array
 */
function Atv_getKegiatanPresensiResource(): array
{
    $data = Atv_getRepoKegiatanPresensi();
    $result = [];
    foreach ($data as $key => $value) $result[$value['id']] = $value['nilai'];
    return Cache::remember('res-kegiatan-presensi', (60 * 60 * 2/* 2 hours */), function () use ($result) {
        return $result;
    });
}

/**
 * ! get resources data kegiatan tambahan
 * for DB processing
 *
 * @return array
 */
function Atv_getKegiatanTambahanResource(): array
{
    $data = Atv_getRepoKegiatanTambahan();
    $result = [];
    foreach ($data as $key => $value) $result[$value['id']] = $value['nilai'];
    return Cache::remember('res-kegiatan-tambahan', (60 * 60 * 2/* 2 hours */), function () use ($result) {
        return $result;
    });
}

/**
 * ! get resources kegiatan presensi average nilai
 * for DB processing
 *
 * @return array
 */
function Atv_getPresensiAvgNilai(): array
{
    $resPresensi = Atv_getRepoKegiatanPresensi();
    $arrPresensiNilai = [];
    for ($i = 0; $i < count($resPresensi); $i++) {
        $arrPresensiNilai[] = [
            'id' => $resPresensi[$i]['id'],
            'avg' => $resPresensi[$i]['nilai_avg']
        ];
    }
    return Cache::remember('res-kegiatan-presensi-avg-nilai', (60 * 60 * 2/* 2 hours */), function () use ($arrPresensiNilai) {
        return $arrPresensiNilai;
    });
}

/**
 * ! get resources data presensi
 * for DB processing
 *
 * @param string $id_semester
 * @return array
 */
function Atv_getPresensiResource($id_semester): array
{
    $data = Presensi::getPresensiResource($id_semester)->get()->map->presensiResourceMap();
    $result = [];
    foreach ($data as $key => $value) $result[$value['id_siswa']][] = $value;
    return Cache::remember('res-presensi', (60 * 60 * 2/* 2 hours */), function () use ($result) {
        return $result;
    });
}

/**
 * ! get resources data nilai tambahan
 * for DB processing
 *
 * @param string $id_semester
 * @return array
 */
function Atv_getNilaiTambahanResource($id_semester): array
{
    $data = NilaiTambahan::getNilaiTambahanResource($id_semester)->get()->map->nilaitambahanResourceMap();
    $result = [];
    foreach ($data as $key => $value) $result[$value['id_siswa']][] = $value;
    return Cache::remember('res-nilaitambahan', (60 * 60 * 2/* 2 hours */), function () use ($result) {
        return $result;
    });
}

/**
 * ! check if filling in attendance is allowed
 *
 * @param string $day
 * @param time $time_start
 * @param time $time_end
 * @return boolean
 */
function Atv_boolPresensiTimeAllowed($day, $time_start, $time_end): bool
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
 * @return string
 */
function Atv_setAksesKegiatan($akses): string
{
    if ($akses == 'presensi') return '5';
    elseif ($akses == 'tambahan') return '7';
}

/**
 * get akses kegiatan type
 * for Human
 *
 * @param numeric $akses
 * @return string
 */
function Atv_HumanAksesKegiatan($akses): string
{
    if ($akses == '5') return 'Presensi';
    elseif ($akses == '7') return 'Tambahan';
}

/**
 * convert presence approve code to string
 *
 * @param string $approve
 * @return string
 */
function Atv_convApproveCodeToString($approve): string
{
    return $approve == '7' ? 'Sudah' : 'Belum';
}

/**
 * get last id presensi group
 *
 * @return integer
 */
function Atv_getLastIdPresensi(): int
{
    return PresensiGroup::orderByDesc('id')->first('id')->id;
}

/**
 * convert to numeric day from request
 * ex: mon (use: shortEnglishDayOfWeek)
 *
 * @param string $day
 * @return string
 */
function Atv_setDayKegiatan($day): string
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
 * @return string
 */
function Atv_getInfoDayKegiatan($day, $locale = false): string
{
    if ($day == '*') return $locale ? 'Setiap hari' : 'Every day';
    else return Carbon_HumanDayNameOfWeek($day, $locale);
}
