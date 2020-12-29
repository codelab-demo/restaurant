<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('dayofweek', [$this, 'dayOfWeek']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('isAvailableToReserve', [$this, 'isAvailableToReserve']),
            new TwigFunction('prepareReservationTime', [$this, 'prepareReservationTime']),
        ];
    }

    public function dayOfWeek($day)
    {
        switch($day) {
            case 1: return 'Monday';
            case 2: return 'Tuesday';
            case 3: return 'Wednesday';
            case 4: return 'Thursday';
            case 5: return 'Friday';
            case 6: return 'Saturday';
            case 7: return 'Sunday';
        }
        return "Wrong name";
    }

    public function isAvailableToReserve($date, $time) {
        return strtotime(date('d-m-Y'))  <  strtotime($date) ||  (strtotime(date('d-m-Y'))  ==  strtotime($date) && strtotime(date('H:i')) < strtotime($time));
    }

    public function prepareReservationTime($date, $time) {
        return strtotime($date." ". $time);
    }
}
