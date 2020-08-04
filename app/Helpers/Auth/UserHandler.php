<?php

/**
 * use libraries
 */

use Illuminate\Support\Facades\Hash;

/**
 * use models
 */

use App\Models\Auth\User;
use App\Models\Auth\UserBiodata;
use App\Models\Auth\UserStatus;

/** */

/**
 * set user status active
 * for database
 *
 * @param string $status
 * @return void
 */
function User_setActiveStatus($status)
{
    if ($status == 'active') {
        return '7';
    }
    if ($status == 'suspend') {
        return '4';
    }
    if ($status == 'block') {
        return '5';
    }
}

/**
 * get user status active
 * for human
 *
 * @param string $status
 * @return void
 */
function User_getActiveStatus($status)
{
    if ($status == '7') {
        return 'active';
    }
    if ($status == '4') {
        return 'suspend';
    }
    if ($status == '5') {
        return 'block';
    }
    return 'black-list';
}

/**
 * get user active by code
 *
 * @param string $code
 * @return void
 */
function User_getActiveStatusByCode($code)
{
    $user = User::where('code', $code)->first('active');
    return $user && $user['active'] ? $user['active'] : null;
}

/**
 * get user status active condition
 * true or false
 *
 * @param string $code
 * @return void
 */
function User_isActive($code)
{
    $user = User::where('code', $code)->first('active');
    return $user && (User_getActiveStatus($user['active'] == 'active')) ? true : false;
}

/**
 * get user name by code
 *
 * @param string $code
 * @return void
 */
function User_getNameByCode($code)
{
    $user = UserBiodata::where('code', $code)->first('name');
    return $user && $user['name'] ? $user['name'] : null;
}

/**
 * check status user
 *
 * @return void
 */
function User_checkStatus()
{
    return auth()->user()->userstat->status;
}

/**
 * get user status by code
 *
 * @param string $code
 * @return void
 */
function User_getStatusByCode($code)
{
    $user = UserStatus::where('code', $code)->first('status');
    return $user && $user['status'] ? $user['status'] : null;
}

/**
 * set user status
 * for DB
 *
 * @param string $status
 * @return void
 */
function User_setStatus($status)
{
    if ($status == 'admin') {
        return 'greatadmin';
    }
    if ($status == 'kurikulum') {
        return 'themanager';
    }
    if ($status == 'guru') {
        return 'bestteacher';
    }
    if ($status == 'ketuakelas') {
        return 'goodleader';
    }
}

/**
 * get user status
 * for Human
 *
 * @param string $status
 * @return void
 */
function User_getStatus($status)
{
    if ($status == 'greatadmin') {
        return 'admin';
    }
    if ($status == 'themanager') {
        return 'kurikulum';
    }
    if ($status == 'bestteacher') {
        return 'guru';
    }
    if ($status == 'goodleader') {
        return 'ketuakelas';
    }
}

/**
 * get user status
 * convert for Human
 *
 * @param string $status
 * @return void
 */
function User_getStatusForHuman($status)
{
    if ($status == 'greatadmin') {
        return 'Admin';
    }
    if ($status == 'themanager') {
        return 'Kurikulum';
    }
    if ($status == 'bestteacher') {
        return 'Guru';
    }
    if ($status == 'goodleader') {
        return 'Ketua Kelas';
    }
}

/**
 * create new user code
 *
 * @return void
 */
function User_createNewCode()
{
    return randString(64);
}

/**
 * create bcrypt password
 *
 * @param string $password
 * @return void
 */
function User_encPass($password)
{
    return Hash::make($password);
}
