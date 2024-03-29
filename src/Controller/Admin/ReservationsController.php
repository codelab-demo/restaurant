<?php

namespace App\Controller\Admin;

use App\Entity\Board;
use App\Entity\Reservation;
use App\Entity\ReservationDetail;
use App\Repository\BoardRepository;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use App\Service\DateHelper;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;


/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
class ReservationsController extends AbstractController
{

    private $router;
    private $dateHelper;
    private $reservationLogger;

    public function __construct(RouterInterface $router, DateHelper $dateHelper, LoggerInterface $reservationLogger)
    {
        $this->router = $router;
        $this->dateHelper = $dateHelper;
        $this->reservationLogger = $reservationLogger;
    }
    /**
     * Show all reservations per one day
     *
     * @Route("/admin/reservations/{date?}", name="app_admin_reservations")
     */
    public function dailyReservations($date, ReservationRepository $reservationRepository, BoardRepository $boardRepository): Response
    {

        if(\DateTime::createFromFormat('d-m-Y', $date) === false) {
            $this->addFlash('notice', 'Wrong date format');
            $date = null;
        }
        $tables = $boardRepository->findAll();
        $dailyReservations = $reservationRepository->findDailyReservations($date);
        $reservationList = [];

        foreach ($tables as $table) {
            $reservationList[$table->getId()]['name'] = $table->getName();
            if($table->getIsChef())
                $reservationList[$table->getId()]['type'] = 'chef';
            elseif($table->getIsFamily())
                $reservationList[$table->getId()]['type'] = 'family';
            else
                $reservationList[$table->getId()]['type'] = 'standart';
        }

        foreach ($dailyReservations as $reservation) {

            $reservationList[$reservation->getTableDetails()->getId()][$reservation->getTime()->format('H:i')]['status'] = $reservation->getStatus();
            $reservationList[$reservation->getTableDetails()->getId()][$reservation->getTime()->format('H:i')]['persons'] = $reservation->getNumberOfPersons();
            $reservationList[$reservation->getTableDetails()->getId()][$reservation->getTime()->format('H:i')]['hash'] = $reservation->getHash();
            $reservationList[$reservation->getTableDetails()->getId()][$reservation->getTime()->format('H:i')]['id'] = $reservation->getId();

        }

        $reservationTimes = [
            '16:00',
            '18:00',
            '20:00',
            '22:00'
        ];


        return $this->render('Admin/dailyReservation.html.twig', [
            'dailyReservations' => $reservationList,
            'dayDate'=>(is_null(($date)) ? date('d-m-Y'):$date),
            'reservationTimes' => $reservationTimes
        ]);
    }

    /**
     * @Route("/admin/reservation/{hash}", name="app_admin_reservation_details")
     */
    public function ReservationDetails(Reservation $reservation): Response
    {

        return $this->render('Admin/reservationDetails.html.twig', [
            'reservation' => $reservation
        ]);
    }

    /**
     * @Route("/admin/reservation/{hash}/cancel", name="app_admin_reservation_cancel")
     */
    public function cancelReservation(Reservation $reservation, EntityManagerInterface $em): Response
    {

        $data = $reservation->getDate();
        if($reservation) {
            $em->remove($reservation);
            $em->flush();
            $this->reservationLogger->warning('Admin: Reservation on'.$this->dateHelper->tableForLogger($reservation).' has been removed');
            $this->addFlash('success','Reservation has been deleted.');
        }

        return new RedirectResponse($this->router->generate('app_admin_reservations', array('date' => $data->format('d-m-Y'))));
    }

    /**
     * @Route("/admin/reservation/{hash}/close", name="app_admin_reservation_close")
     */
    public function closeReservation(Reservation $reservation, EntityManagerInterface $em, Request $request): Response
    {

        if ($request->isMethod('GET') && $reservation && $reservation->getStatus() == "Accepted") {
            $date = $reservation->getDate()->format('d-m-Y');
            $reservation->setStatus("Closed");
            $em->persist($reservation);
            $em->flush();
            $this->reservationLogger->warning('Admin: Reservation on '.$this->dateHelper->tableForLogger($reservation).' has been closed');
            $this->addFlash('success', 'Reservation has been closed');

        } else {
            $date = date('d-m-Y');
        }

        return new RedirectResponse($this->router->generate('app_admin_reservations', array('date' => $date)));
    }
    /**
     * @Route("/admin/reservation/add/{date}/{time}/{table}", name="app_admin_reservation_add")
     */
    public function addReservation($date, $time, $table, EntityManagerInterface $em, Request $request, LoggerInterface $logger): Response
    {
        $errors = false;
        $persons = intVal($request->request->get('persons'));
        $name = filter_var($request->request->get('name'),FILTER_SANITIZE_STRING);
        $phone = filter_var($request->request->get('phone'),FILTER_SANITIZE_STRING);

        $errors = $this->validateInput($date, $logger, $time);

        $tableRepo = $em->getRepository(Board::class);
        $tableInfo = $tableRepo->find($table);

        $reservationRepo = $em->getRepository(Reservation::class);

        $reservation = $this->getReservation($reservationRepo, $date, $time, $tableInfo);

        $this->checkIsTableReserved($reservation, $date, $time, $tableInfo, $errors);

        if($errors) {
            $logger->error('Reservation process aborted.');
            return new RedirectResponse($this->router->generate('app_admin_reservations'));
        }


        if ($request->isMethod('POST') && !$reservation) {
            $reservation = new Reservation();


            if($tableInfo->getNumberOfPersons() < $persons || $tableInfo->getMinNumberOfPersons() > $persons) {
                $logger->error('Wrong number of persons, received persons: '.$persons.' out of range: '.$tableInfo->getMinNumberOfPersons().'-'.$tableInfo->getNumberOfPersons());
                $errors= true;
                $this->addFlash('error', 'Wrong number of persons');
            } else {
                $reservation->setTableDetails($tableInfo);
                $reservation->setStatus("Accepted");
                $reservation->setContactPhone($phone);
                $reservation->setContactName($name);
                $reservation->setDate(\DateTime::createFromFormat('d-m-Y', $date));
                $reservation->setReservationDate(\DateTime::createFromFormat('Y-m-d H:i', $date." ".$time));
                $reservation->setTime(\DateTime::createFromFormat('H:i', $time));
                $reservation->setNumberOfPersons($persons);
                $reservation->setIp($request->getClientIp());
                $reservation->setHash(hash('sha1', $date . $time . $tableInfo->getId()));
                $em->persist($reservation);
                $em->flush();
                $this->reservationLogger->info('Admin: New reservation on '.$this->dateHelper->tableForLogger($reservation));
                $this->addFlash('success', 'Reservation has been added');
                return new RedirectResponse($this->router->generate('app_admin_reservations'));
            }


        }

        return $this->render('Admin/addReservation.html.twig', [
            'day' => \DateTime::createFromFormat('d-m-Y', $date),
            'time' => \DateTime::createFromFormat('H:i', $time),
            'table' => $tableInfo,
            'name' => $name,
            'phone'=> $phone
        ]);
    }

    /**
     * @param $date
     * @param LoggerInterface $logger
     * @param $time
     * @return bool
     */
    public function validateInput($date, LoggerInterface $logger, $time): bool
    {
        $errors = false;

        if (\DateTime::createFromFormat('d-m-Y', $date) === false) {
            $errors = true;
            $logger->error('Admin: Wrong date during add reservation, received: ' . $date);
            $this->addFlash('error', 'Wrong date, please select correct one');
        }

        if (\DateTime::createFromFormat('H:i', $time) === false || !in_array($time, array('16:00', '18:00', '20:00', '22:00'))) {
            $errors = true;
            $logger->error('Admin: Wrong time during add reservation, received: ' . $time);
            $this->addFlash('error', 'Wrong time, please select correct one');
        }
        return $errors;
    }

    /**
     * @param $reservationRepo
     * @param $date
     * @param $time
     * @param $tableInfo
     * @param $errors
     * @return array
     */
    public function checkIsTableReserved($reservation, &$errors)
    {
        if ($reservation) {
            $errors[] = 'Table is already reserved at this time.';
            $this->addFlash('error', 'Table is already reserved');
        }

    }

    /**
     * @param $reservationRepo
     * @param $date
     * @param $time
     * @param $tableInfo
     * @return mixed
     */
    public function getReservation($reservationRepo, $date, $time, $tableInfo)
    {
        $reservation = $reservationRepo->findBy([
            'date' => \DateTime::createFromFormat('d-m-Y', $date),
            'time' => \DateTime::createFromFormat('H:i', $time),
            'tableDetails' => $tableInfo
        ]);
        return $reservation;
    }
}
