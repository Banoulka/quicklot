<?php


namespace app\models\lib;


use DateInterval;
use DateTime;

use Carbon\Carbon;

class Helpers
{
    public static function className($class)
    {
        $classname = get_class($class);
        if ($pos = strrpos($classname, '\\')) return substr($classname, $pos + 1);
        return $pos;
    }

    public static function staticClass($class)
    {
        if ($pos = strrpos($class, '\\')) return substr($class, $pos + 1);
        return $pos;
    }

    public static function readableDate(string $dateString)
    {
        return date('D jS M Y g:ia', strtotime($dateString));
    }

    public static function formatInterval(DateInterval $interval) {
        $result = "";
        if ($interval->y) { $result .= $interval->format("%y years "); }
        if ($interval->m) { $result .= $interval->format("%m mon "); }
        if ($interval->d) { $result .= $interval->format("%d d "); }
        if ($interval->h) { $result .= $interval->format("%h hr "); }
        if ($interval->i) { $result .= $interval->format("%i min "); }
        if ($interval->s) { $result .= $interval->format("%s sec "); }

        return $result;
    }

    public static function timeElapse($datetime) {
        return Carbon::parse($datetime)->diffForHumans([
            'options' => Carbon::ONE_DAY_WORDS|CARBON::JUST_NOW
        ]);
    }
}