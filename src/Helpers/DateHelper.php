<?php namespace Staskjs\LaravelUtils\Helpers;

use \Carbon\Carbon;

const DB_DATE_FORMAT = 'd-m-Y H:i:s';

class DateHelper
{
    public static function formatDate($date, $format = DB_DATE_FORMAT) {
        return Carbon::parse($date)->format($format);
    }

    public static function dbDate($date) {
        return self::formatDate($date, 'Y-m-d');
    }

    public static function dbDatetime($date) {
        return self::formatDate($date, 'Y-m-d H:i:s');
    }

    public static function ukDate($date) {
        return self::formatDate($date, 'd/m/Y');
    }

    public static function ukDatetime($date) {
        return self::formatDate($date, 'd/m/Y H:i:s');
    }
}
