<?php

namespace App\Controller\Tools;

use App\Entity\Board;
use App\Entity\Reservation;
use App\Repository\BoardRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class ReservationController extends AbstractController
{

    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/do_reservation", name="do_reservation", methods={"POST"})
     */
    public function times(Request $request, EntityManagerInterface $em,BoardRepository $board, ReservationRepository $reservationRepo, LoggerInterface $logger): Response
    {

        $response = [];

        $requestDate = $request->get('date');
        $requestTime = $request->get('time');
        $requestTable = intVal($request->get('table'));
        $requestPersons = intVal($request->get('persons'));
        $requestName = filter_var($request->get('name'), FILTER_SANITIZE_STRING);
        $requestPhone = filter_var($request->get('phone'), FILTER_SANITIZE_NUMBER_INT);

        $errors = [];

        if(\DateTime::createFromFormat('Y-m-d', $requestDate) === false)
            $errors[] = 'Wrong date, please select correct one.';

        if($time = \DateTime::createFromFormat('H:i', $requestTime) === false || !in_array($requestTime,array('16:00','18:00', '20:00', '22:00')))
            $errors[] = 'Wrong time, please select correct one.';


        $table = $board->find($requestTable);

        $earlier = new \DateTime("today midnight");
        $later = new \DateTime($requestDate);

        $diff = $earlier->diff($later)->format("%r%a");

        if($diff < 0 || $diff > 11) {
            $errors[] = 'Wrong date';
        }

        if(!$table) {
            $errors[] = 'Wrong table, please select correct one';
        } else {
            if($table->getMinNumberOfPersons() > $requestPersons ||$table->getNumberOfPersons() < $requestPersons) {
                $errors[] = 'Wrong number of persons';
            }
        }

        if(!$requestName || !$requestPhone)
            $errors[] = 'Wrong contact data, please provide correct Name and phone';

        $reservation = $reservationRepo->findBy([
            'time' => \DateTime::createFromFormat('H:i', $requestTime),
            'date' => \DateTime::createFromFormat('Y-m-d', $requestDate),
            'tableDetails' => $table
        ]);
        if($reservation) {
            $errors[] = 'Table just reserved. Please choose other options.';
        }

        if(empty($errors)) {
            $reservation = new Reservation();
            $reservation->setTime(\DateTime::createFromFormat('H:i', $requestTime));
            $reservation->setDate(\DateTime::createFromFormat('Y-m-d', $requestDate));
            $reservation->setNumberOfPersons($requestPersons);
            $reservation->setStatus("Accepted");
            $reservation->setTableDetails($table);
            $reservation->setHash(hash('sha1', $requestDate . $requestTime . $table->getId()));
            $reservation->setContactName($requestName);
            $reservation->setContactPhone($requestPhone);
            $reservation->setIp($request->getClientIp());
            $reservation->setReservationDate(new \DateTime("now"));

            try {
                $em->persist($reservation);
                $em->flush();
                $this->session->set('new_reservation', \DateTime::createFromFormat('Y-m-d', $requestDate)->format('jS F').' at '.$requestTime.' on '.$table->getName());
                $response['code'] = 100;
                $response['hash'] = $reservation->getHash();
            } catch (\Exception $e) {
                $response['code'] = 200;
                $response['errors'] = array('Error during saving reservation. Please contact us.');
                $logger->error('Error during saving reservation:'. $e);
            }
        } else {
            $response['code'] = 200;
            $response['errors'] = $errors;
        }

        return new JsonResponse($response);

    }


}
