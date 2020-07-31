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
 * jwt auth default route
 * access : /api/auth/---
 */
Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', 'APIs\Auth\AuthController@login');
    Route::post('/logout', 'APIs\Auth\AuthController@logout');
    Route::get('/creds', 'APIs\Auth\AuthController@credential');
});

Route::group(['middleware' => ['auth:sanctum', 'useractive:active'], 'prefix' => 'access'], function () {
    Route::resource('/user/management', 'APIs\Access\UserManagementController');
    Route::resource('/user/recover-password', 'APIs\Access\ForgetPasswordController');
});
