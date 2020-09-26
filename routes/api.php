<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 * authentication api route
 * access : /api/auth/---
 */
Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', 'APIs\Auth\AuthController@login');
    Route::post('/logout', 'APIs\Auth\AuthController@logout');
    Route::get('/creds', 'APIs\Auth\AuthController@credential');
});

/**
 * access resources
 * middleware : have token, status active
 * access : /api/access/---
 */
Route::group(['middleware' => ['auth:sanctum', 'useractive:active'], 'prefix' => 'access'], function () {
    Route::resource('/user/management', 'APIs\Access\UserController');
    Route::resource('/user/recover-password', 'APIs\Access\ForgetPasswordController');
});

/**
 * project resources
 * version 1
 * middleware : have token, status active
 * access : /api/v1/---
 */
Route::group(['middleware' => ['auth:sanctum', 'useractive:active'], 'prefix' => 'v1'], function () {
    Route::resource('/kelas', 'APIs\School\Curriculum\KelasController');
    Route::resource('/siswa', 'APIs\School\Actor\SiswaController');
    Route::resource('/ketua-kelas', 'APIs\School\Actor\KetuaKelasController');
    Route::resource('/semester', 'APIs\School\Curriculum\SemesterController');
    Route::resource('/kegiatan', 'APIs\School\Activity\KegiatanController');
    Route::resource('/nilai-tambahan', 'APIs\School\Activity\NilaiTambahanController');
    Route::resource('/presensi', 'APIs\School\Activity\PresensiController');
    Route::resource('/nilai-akhir', 'APIs\School\Curriculum\NilaiAkhirController');
});

Route::get('/test/api', function () {
    $getKelas = \App\Models\School\Curriculum\Kelas::getActiveKelas();
    $data = [];
    for ($i = 0; $i < $getKelas->count(); $i++) {
        for ($j = 0; $j < count($getKelas->get()[$i]->siswa); $j++) {
            $data[] = (new \App\Services\School\Curriculum\NilaiAkhirService($getKelas->get()[$i]->siswa[$j]->id, Cur_getActiveIDSemesterNow()))->generate();
        }
    }
    return ($data);
})->middleware(['auth:sanctum', 'useractive:active']);

Route::get('/test/web/{id}', function ($id) {
    // $siswa = \App\Models\School\Actor\Siswa::find($id);
    // $presensi = $siswa->presensi->where('id_semester', 12);
    // $data = [];
    // foreach ($presensi as $key => $value) {
    //     if ($presensi[$key]->presensigroup->approve == '7') {
    //         $data[] = $value;
    //     }
    // }
    // return collect($data)->where('presensigroup.id_kegiatan', 1);
    //
    // return (new \App\Services\School\Curriculum\NilaiAkhirService($id, Cur_getActiveIDSemesterNow()))->generate();
    return Carbon_DBDayNumOfWeek();
});
