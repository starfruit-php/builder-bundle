<?php

namespace Starfruit\BuilderBundle\Tool;

use Carbon\Carbon;

class TimeTool
{
    public static function unixtime2string($unixtime, $format = 'd/m/Y')
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
}
