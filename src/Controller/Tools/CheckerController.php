<?php

namespace App\Controller\Tools;

use App\Entity\Reservation;
use App\Repository\BoardRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class CheckerController extends AbstractController
{
    /**
     * @Route("/time_checker", name="time_checker", methods={"POST"})
     */
    public function times(Request $request, EntityManagerInterface $em,ReservationRepository $reservationRepository, BoardRepository $boardRepository): Response
    {
        $date = $request->get('date');
        $token = $request->get('token');

        $isValidToken = $this->isCsrfTokenValid($date, $token);

        if(!$isValidToken) {
            throw new BadRequestHttpException('Wrong query');
        }

        $times = $reservationRepository->findTimes($date,$boardRepository->totalTables());

        $serializer = new Serializer(array(new DateTimeNormalizer()));

        foreach($times as $id => $time) {
            $times[$id]['time'] = $serializer->normalize(
                $time['time'],
                null,
                array(DateTimeNormalizer::FORMAT_KEY => 'H:i')
            );
        }

        return new JsonResponse($times);

    }

    /**
     * @Route("/table_checker", name="table_checker")
     */
    public function tables(Request $request, SerializerInterface $serializer, EntityManagerInterface $em,ReservationRepository $reservationRepository, BoardRepository $boardRepository): Response
    {

        $tables = $boardRepository->findAll();

        $date = $request->get('date');
        $time = $request->get('time');
        $token = $request->get('token');

        $isValidToken = $this->isCsrfTokenValid($date, $token);
        if(!$isValidToken) {
            throw new BadRequestHttpException('Wrong query');
        }
        $reservations = [];
        foreach ($tables as $table) {
           $reservations[$table->getId()]['tableName'] = $table->getName();
           $reservations[$table->getId()]['numberOfPersons'] = $table->getNumberOfPersons();
           $reservations[$table->getId()]['minNumberOfPersons'] = $table->getMinNumberOfPersons();
           $reservations[$table->getId()]['tooltip'] = $table->getTooltip();
           if($table->getIsChef()) {
               $reservations[$table->getId()]['type'] = "chef";
           } elseif ($table->getIsFamily()) {
               $reservations[$table->getId()]['type'] = "family";
           } else {
               $reservations[$table->getId()]['type'] = "standard";
           }
            $reservations[$table->getId()]['free'] = 'yes';
        }

        $repo = $em->getRepository(Reservation::class);
        $daily = $repo->findBy([
            'date' => \DateTime::createFromFormat('Y-m-d', $date),
            'time' => \DateTime::createFromFormat('H:i', $time)]);

        foreach ($daily as $pos) {
            $reservations[$pos->getTableDetails()->getId()]['free'] = 'no';
        }

        return new JsonResponse($reservations);

    }
}
