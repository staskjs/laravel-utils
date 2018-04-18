<?php namespace Staskjs\LaravelUtils\Helpers;

use \Carbon\Carbon;

const DB_DATE_FORMAT = 'd-m-Y H:i:s';

class DateHelper
{
    public static function formatDate($date, $format = DB_DATE_FORMAT) {
        return Carbon::parse($date)->format($format);
    }
}
