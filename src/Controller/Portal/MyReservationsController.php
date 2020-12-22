<?php

namespace App\Controller\Portal;

use App\Entity\Reservation;
use App\Entity\ReservationDetail;
use App\Repository\ReservationDetailRepository;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class MyReservationsController extends AbstractController
{

    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/my_reservations", name="app_my_reservations")
     */
    public function index(Request $request, ReservationRepository $reservation, ReservationDetailRepository $reservationDetailRepository): Response
    {
        $ip = $request->getClientIp();

        $myReservations = $reservation->findBy([
            'ip'=> $ip
        ]);
        $new_reservation = null;
        if($this->session->has('new_reservation')) {
            $new_reservation = $this->session->get('new_reservation');
            $this->session->remove('new_reservation');
        }

        return $this->render('Portal/myReservations.html.twig', [
            'myReservations' => $myReservations,
            'new_reservation'=> $new_reservation,
        ]);
    }

    /**
     * @Route("/demo_reservations", name="app_demo_reservations")
     */
    public function demo(Request $request, ReservationRepository $reservation, ReservationDetailRepository $reservationDetailRepository): Response
    {
        $ip = $request->getClientIp();

        $myReservations = $reservation->findRandom();

        return $this->render('Portal/myReservations.html.twig', [
            'myReservations' => $myReservations,
            'new_reservation'=> null,
        ]);
    }
}
