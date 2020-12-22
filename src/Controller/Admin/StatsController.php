<?php

namespace App\Controller\Admin;

use App\Repository\ReservationDetailRepository;
use App\Repository\ReservationRepository;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatsController extends AbstractController
{
    /**
     * @Route("/admin/stats", name="app_admin_stats")
     */
    public function index(ReservationRepository $reservationRepository, ReservationDetailRepository $reservationDetailRepository): Response
    {
        $reservations = $reservationRepository->getMonthlyReservation(date('d-m-Y'));
        $earnings = $reservationDetailRepository->getMonthlyearnings(date('d-m-Y'));


        return $this->render('Admin/stats.html.twig', [
            'earnings'=>$earnings,
            'reservations'=>$reservations
        ]);
    }
}
