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
 * @return string
 */
function Arr_random($array_data): string
{
    return Arr::random($array_data);
}

/**
 * get only the specified key from array data
 * similar with : array_column()
 *
 * @param array $array_data
 * @param string $key
 * @return array
 */
function Arr_pluck($array_data, $key): array
{
    return Arr::pluck($array_data, $key);
}

/**
 * collapse an array into slim version
 *
 * @param array $array_data
 * @return array
 */
function Arr_collapse($array_data): array
{
    return Arr::collapse($array_data);
}

/**
 * unserialize without error
 *
 * @param string $data
 * @return array
 */
function Arr_unserialize($data): array
{
    try {
        return unserialize($data);
    } catch (\Throwable $th) {
        return $th;
    }
}
