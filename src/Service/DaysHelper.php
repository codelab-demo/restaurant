<?php


namespace App\Service;


class DaysHelper
{
    public static function daysOfWeek(): array
    {
        return array(
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday'
        );
    }
}