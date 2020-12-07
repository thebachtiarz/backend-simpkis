<?php

/**
 * use libraries
 */

use Illuminate\Support\Str;

/**
 * use models
 *
 */

/** */

/**
 * create custom amount random string
 *
 * @param integer $rand_amount
 * @return string
 */
function Str_random($rand_amount): string
{
    return Str::random($rand_amount);
}

/**
 * filter a sentence into string only
 *
 * @param string $sentence
 * @return string
 */
function Str_pregStringOnly($sentence): string
{
    return preg_replace("/[^A-Za-z?![:space:]]/", '', $sentence);
}
