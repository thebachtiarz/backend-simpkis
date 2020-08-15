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
 * @param int $rand_amount
 * @return void
 */
function Str_random($rand_amount)
{
    return Str::random($rand_amount);
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
