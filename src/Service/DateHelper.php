<?php

namespace App\Service;

use App\Entity\Reservation;

class DateHelper
{
    public function tableForLogger(Reservation $reservation): string
    {

        return 'table '.$reservation->getTableDetails()->getName().' at '.$reservation->getReservationDate()->format('Y-m-d H:i');
    }

}