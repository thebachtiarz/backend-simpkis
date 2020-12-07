<?php

/**
 * use libraries
 */

use Illuminate\Support\Facades\Hash;

/**
 * use models
 */

use App\Models\Auth\User;

/** */

/**
 * set user status active
 * for database
 *
 * @param string $status
 * @return string
 */
function User_setActiveStatus($status): string
{
    $userCondition = [
        'active' => '7',
        'suspend' => '4',
        'block' => '5'
    ];
    return (bool) $status ? $userCondition[$status] : '9';
}

/**
 * get user status active
 * for human
 *
 * @param numeric $status
 * @return string
 */
function User_getActiveStatus($status): string
{
    $userCondition = [
        '7' => 'active',
        '4' => 'suspend',
        '5' => 'block'
    ];
    return (bool) $status ? $userCondition[$status] : 'black-list';
}

/**
 * get user active by id
 *
 * @param numeric $id
 * @return string
 */
function User_getActiveStatusById($id): string
{
    $user = User::find($id);
    return (bool) $user ? $user->active : null;
}

/**
 * get user status active condition
 *
 * @param numeric $id
 * @return boolean
 */
function User_isActive($id): bool
{
    $user = User::find($id);
    return ((bool) $user && ($user->active == User_setActiveStatus('active'))) ? true : false;
}

/**
 * get user name by id
 *
 * @param numeric $id
 * @return string
 */
function User_getNameById($id): string
{
    $user = User::find($id);
    return (bool) $user ? $user->userbio->name : null;
}

/**
 * check status user
 *
 * @return string
 */
function User_checkStatus(): string
{
    return auth()->user()->userstat->status;
}

/**
 * get user status by id
 *
 * @param numeric $id
 * @return string
 */
function User_getStatusById($id): string
{
    $user = User::find($id);
    return (bool) $user ? $user->userstat->status : null;
}

/**
 * set user status
 * for DB
 *
 * @param string $status
 * @return string
 */
function User_setStatus($status): string
{
    $userAny = [
        'admin' => 'greatadmin',
        'kurikulum' => 'themanager',
        'guru' => 'bestteacher',
        'ketuakelas' => 'goodleader'
    ];
    return (bool) $status ? $userAny[$status] : '';
}

/**
 * get user status
 * for Human
 *
 * @param string $status
 * @return string
 */
function User_getStatus($status): string
{
    $userAny = [
        'greatadmin' => 'admin',
        'themanager' => 'kurikulum',
        'bestteacher' => 'guru',
        'goodleader' => 'ketuakelas'
    ];
    return (bool) $status ? $userAny[$status] : '';
}

/**
 * get user status
 * convert for Human
 *
 * @param string $status
 * @return string
 */
function User_getStatusForHuman($status): string
{
    $userAny = [
        'greatadmin' => 'Admin',
        'themanager' => 'Kurikulum',
        'bestteacher' => 'Guru',
        'goodleader' => 'Ketua Kelas'
    ];
    return (bool) $status ? $userAny[$status] : '';
}

/**
 * create new user code
 *
 * @return string
 */
function User_createNewCode(): string
{
    return Str_random(64);
}

/**
 * create bcrypt password
 *
 * @param string $password
 * @return string
 */
function User_encPass($password): string
{
    return Hash::make($password);
}
