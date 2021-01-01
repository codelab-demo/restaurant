<?php

namespace App\Controller\Portal;

use App\Entity\Board;
use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

class TableReservationController extends AbstractController
{
    /**
     * @Route("/reservation", name="app_reservation")
     */
    public function index(EntityManagerInterface $em, CacheInterface $cache): Response
    {

        $repoArticles = $em->getRepository(Board::class);
        $totalTables = $repoArticles->totalTables();


        $maxDailyReservations = $totalTables*4;

        $table_repo = $em->getRepository(Reservation::class);
        $reservationDays = $table_repo->findReservations($maxDailyReservations);

        $reservationsArray = [];

        for($i=1;$i<13;$i++) {
//            $listOfDays[date('Y-m-d',strtotime('+'.$i.' days'))]['title'] = date('jS F',strtotime('+'.$i.' days'));
            $listOfDays[date('Y-m-d',strtotime('+'.$i.' days'))] = "yes";
        }

        foreach ($reservationDays as $day) {
            $listOfDays[$day['date']->format('Y-m-d')] = $day['free'];
        }

        return $this->render('Portal/tableReservation.html.twig', [
            'listOfDays' => $listOfDays,
            'days' => $reservationsArray
        ]);
    }
}
