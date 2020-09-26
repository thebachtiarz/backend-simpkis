<?php

/**
 * use libraries
 */

use Illuminate\Support\Carbon;

/**
 * use models
 */

/** */

function Carbon_atomConvertDateTime($datetime)
{
    return Carbon::parse($datetime)->format('Y-m-d\TH:i:s.uP');
}
/**
 * convert datetime
 * for DB
 *
 * @param date $datetime
 * @return void
 */
function Carbon_DBConvertDateTime($datetime)
{
    return Carbon::parse($datetime)->toDateTimeString();
}

/**
 * get date time now
 * for DB
 *
 * @return void
 */
function Carbon_DBtimeNow()
{
    return Carbon::now()->toDateTimeString();
}

/**
 * get date time today
 * for DB
 *
 * @return void
 */
function Carbon_DBdatetimeToday()
{
    return Carbon::today()->toDateTimeString();
}

/**
 * get day of week in number
 *
 * @return void
 */
function Carbon_DBDayNumOfWeek($date = '')
{
    return Carbon::parse($date)->dayOfWeekIso;
}

/**
 * get day of week in name
 *
 * @param boolean $locale
 * @return void
 */
function Carbon_HumanDayNameOfWeek($date = '', $locale = false)
{
    $day = Carbon::create(carbon::getDays()[$date]);
    if ($locale) $day->locale('id_ID');
    return $day->dayName;
}

/**
 * check if today is work day
 *
 * @return void
 */
function Carbon_IsWorkDayNow()
{
    $result = false;
    $getNumDayNow = Carbon::today()->dayOfWeekIso;
    if (($getNumDayNow >= 1) && ($getNumDayNow <= 5)) $result = true;
    return $result;
}

/**
 * get time now
 *
 * @return void
 */
function Carbon_AnyTimeNow()
{
    return Carbon::now()->toTimeString();
}

/**
 * get full date time now
 * for human
 *
 * @return void
 */
function Carbon_HumanFullDateTimeNow()
{
    return Carbon::now()->format('l, d F Y H:i:s');
}

/**
 * parse full date time
 * for human
 *
 * @param date $datetime
 * @return void
 */
function Carbon_HumanFullDateTime($datetime)
{
    return Carbon::parse($datetime)->format('l, d F Y H:i:s');
}

/**
 * parse date time
 * for human
 *
 * @param date $datetime
 * @return void
 */
function Carbon_HumanDateTime($datetime)
{
    return Carbon::parse($datetime)->format('d F Y H:i:s');
}

/**
 * convert date time to date only
 * for DB
 *
 * @param date $datetime
 * @return void
 */
function Carbon_DBDateParse($datetime)
{
    return Carbon::parse($datetime)->format('Y-m-d');
}

/**
 * convert date time to date only
 * for Any
 *
 * @param date $datetime
 * @return void
 */
function Carbon_AnyDateParse($datetime)
{
    return Carbon::parse($datetime)->format('Ymd');
}

/**
 * convert string to time
 * ex: 09:30
 *
 * @param numeric $time
 * @return void
 */
function Carbon_AnyTimeParse($time = '')
{
    return Carbon::parse($time ? $time : '00:00')->toTimeString();
}

/**
 * convert datetime to date only
 * for Human
 *
 * @param date $datetime
 * @return void
 */
function Carbon_HumanDateParse($datetime)
{
    return Carbon::parse($datetime ? $datetime : Carbon_DBdatetimeToday())->format('d F Y');
}

/**
 * convert datetime to display simple date
 * for Human
 *
 * @param date $datetime
 * @return void
 */
function Carbon_HumanDateSimpleDisplayParse($datetime = '')
{
    return Carbon::parse($datetime ? $datetime : Carbon_DBtimeNow())->format('D, M j');
}

/**
 * convert date time to interval time
 * for Human
 *
 * @param date $datetime
 * @return void
 */
function Carbon_HumanIntervalDateTime($datetime)
{
    return Carbon::parse($datetime)->diffForHumans();
}

/**
 * get interval date created from date updated
 *
 * @param datetime $date_created
 * @param datetime $date_updated
 * @return void
 */
function Carbon_HumanIntervalCreateUpdate($date_created, $date_updated)
{
    return Carbon_AnyConvDateToTimestamp($date_updated) > Carbon_AnyConvDateToTimestamp($date_created) ? Carbon_HumanIntervalDateTime($date_updated) : 'Never';
}

/**
 * convert date time to timestamp
 *
 * @param date $datetime
 * @return void
 */
function Carbon_AnyConvDateToTimestamp($datetime)
{
    return Carbon::parse($datetime)->timestamp;
}

/**
 * get range date time
 * for Human
 *
 * @param date $start
 * @param date $end
 * @return void
 */
function Carbon_HumanRangeDateTimeDuration($start, $end)
{
    $checkStart = Carbon_DBDateParse($start);
    $checkEnd = Carbon_DBDateParse($end);
    // if date is today, then return date only in first
    if ($checkStart == $checkEnd) {
        return Carbon::parse($start)->format('l, d F Y') . '. ' . Carbon::parse($start)->format('H:i') . ' - ' . Carbon::parse($end)->format('H:i');
    } else {
        return Carbon::parse($start)->format('l, d F Y H:i') . ' - ' . Carbon::parse($end)->format('l, d F Y H:i');
    }
}

/**
 * get date yesterday by specified date
 *
 * @param date $datetime
 * @param integer $days
 * @return void
 */
function Carbon_DateSubYesterday($datetime, $days = 1)
{
    return Carbon::parse($datetime)->subDay($days)->toDateTimeString();
}

/**
 * get range date yesterday by today
 *
 * @param string $rangedate
 * @return void
 */
function Carbon_RangeDateYesterday($rangedate)
{
    if ($rangedate == 'today') {
        return Carbon_dateSubYesterday(Carbon_DBdatetimeToday(), 0);
    } elseif ($rangedate == 'yesterday') {
        return Carbon_dateSubYesterday(Carbon_DBdatetimeToday(), 1);
    } elseif ($rangedate == 'last-week') {
        return Carbon_dateSubYesterday(Carbon_DBdatetimeToday(), 7);
    } elseif ($rangedate == 'last-month') {
        return Carbon_dateSubYesterday(Carbon_DBdatetimeToday(), 30);
    } else {
        return Carbon_dateSubYesterday(Carbon_DBdatetimeToday(), 10000);
    }
}
