<?php

/**
 * use libraries
 */

use Illuminate\Support\Arr;

/**
 * use models
 *
 */

/** */

/**
 * get random element from array data
 *
 * @param array $array_data
 * @return void
 */
function Arr_random($array_data)
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
function Arr_pluck($array_data, $key)
{
    return Arr::pluck($array_data, $key);
}

/**
 * collapse an array into slim version
 *
 * @param array $array_data
 * @return void
 */
function Arr_collapse($array_data)
{
    return Arr::collapse($array_data);
}
