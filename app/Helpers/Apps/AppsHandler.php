<?php

/**
 * use libraries
 */

use Illuminate\Support\Str;
use Illuminate\Support\Arr;

/**
 * use models
 *
 */

/** */

/**
 * handler template theme asset
 *
 * @return void
 */
# online version
function online_asset()
{
    return 'http://bachtiars.com/AdminLTE-3.0.2/';
}
# offline version
function offline_asset()
{
    return asset('AdminLTE-3.0.2');
}

/**
 * icon apps
 *
 * @return void
 */
function apps_icon()
{
    return online_asset() . '/dist/img/AdminLTELogo.png';
}

/**
 * default url
 * user profile image
 *
 * @return void
 */
function default_url_user_image()
{
    return '/files/image/profile/users/';
}

/**
 * default user image
 *
 * @return void
 */
function default_user_image()
{
    return '/files/image/profile/default.jpg';
}

/**
 * get ip address client
 *
 * @return void
 */
function getClientIpAddress()
{
    return request()->ip();
}

/**
 * create custom amount random string
 *
 * @param int $rand_amount
 * @return void
 */
function randString($rand_amount)
{
    return Str::random($rand_amount);
}

/**
 * get random element from array data
 *
 * @param array $array_data
 * @return void
 */
function randArray($array_data)
{
    return Arr::random($array_data);
}

/**
 * get only the specified key from array data
 * similar with : array_column()
 *
 * @param array $array_data
 * @param string $key
 * @return void
 */
function pluckArray($array_data, $key)
{
    return Arr::pluck($array_data, $key);
}

/**
 * collapse an array into slim version
 *
 * @param array $array_data
 * @return void
 */
function collapseArray($array_data)
{
    return Arr::collapse($array_data);
}

/**
 * filter a sentence into string only
 *
 * @param string $sentence
 * @return void
 */
function Str_pregStringOnly($sentence)
{
    return preg_replace("/[^A-Za-z?![:space:]]/", '', $sentence);
}
