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
 * @return void
 */
function User_setActiveStatus($status)
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
 * @param string $status
 * @return void
 */
function User_getActiveStatus($status)
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
 * @param int $id
 * @return void
 */
function User_getActiveStatusById($id)
{
    $user = User::find($id);
    return (bool) $user ? $user->active : null;
}

/**
 * get user status active condition
 * boolean
 *
 * @param int $id
 * @return void
 */
function User_isActive($id)
{
    $user = User::find($id);
    return ((bool) $user && ($user->active == User_setActiveStatus('active'))) ? true : false;
}

/**
 * get user name by id
 *
 * @param string $id
 * @return void
 */
function User_getNameById($id)
{
    $user = User::find($id);
    return (bool) $user ? $user->userbio->name : null;
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
 * get user status by id
 *
 * @param string $id
 * @return void
 */
function User_getStatusById($id)
{
    $user = User::find($id);
    return (bool) $user ? $user->userstat->status : null;
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
 * @return void
 */
function User_getStatus($status)
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
 * @return void
 */
function User_getStatusForHuman($status)
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
 * @return void
 */
function User_createNewCode()
{
    return Str_random(64);
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
