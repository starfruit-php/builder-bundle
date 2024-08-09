<?php

namespace Starfruit\BuilderBundle\Tool;

use Carbon\Carbon;

class TimeTool
{
    const FORMAT = 'd/m/Y';

    public static function unixtime2string($unixtime, $format = self::FORMAT)
    {
        if (!$unixtime) {
            return '';
        }

        $time = Carbon::createFromTimestamp($unixtime);
        // $time->setTimezone($timezone);

        return $time->format($format);
    }

    public static function getTimezone()
    {
        return date('P');
    }

    public static function string2Carbon($string, $format = self::FORMAT)
    {
        return Carbon::createFromFormat($format, $string);
    }

    public static function diffDays($from, $to, $format = self::FORMAT)
    {
        $from = self::string2Carbon($from, $format);
        $to = self::string2Carbon($to, $format);
        return $from->diffInDays($to);   
    }
}
