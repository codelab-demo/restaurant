<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function findReservations($maxDailyReservations): ?array
    {
     return $this->createQueryBuilder('r')
        ->select('r.date, count(r.date) as ile, case when count(r.date) >= :maxReservations then \'no\' else \'yes\' end as free')
        ->where('r.date < :toDate')
        ->andWhere('r.date > :now')
        ->addGroupBy('r.date')
        ->getQuery()
        ->setParameter('maxReservations' ,$maxDailyReservations)
        ->setParameter('toDate' ,new \DateTime('today midnight +12 day'))
        ->setParameter('now' ,new \DateTime('today midnight'))
        ->getResult();
    }

    public function findTimes($date, $maxTimeReservations): ?array
    {
        return $this->createQueryBuilder('r')
            ->select('r.time, case when count(r.time) >= :maxReservations then \'no\' else \'yes\' end as free')
            ->where('r.date = :toDate')
            ->addGroupBy('r.date')
            ->addGroupBy('r.time')
            ->getQuery()
            ->setParameter('maxReservations' ,$maxTimeReservations)
            ->setParameter('toDate' ,new \DateTime($date))
            ->getResult();

    }

    public function findRandom() {

        $rows = $this->createQueryBuilder('r')
            ->select('r.id')
            ->getQuery()
            ->getArrayResult();
        $ids = array_column($rows, "id");

        shuffle($ids);
        $ids = array_slice($ids,0,4);

        $rows = $this->createQueryBuilder('r')
            ->andWhere('r.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->orderBy('r.date', 'DESC')
            ->addOrderBy('r.time', 'DESC')
            ->getQuery()
            ->getResult();

        return $rows;

    }

    public function findDailyReservations($date) {
        if(is_null($date))
            $date = "today";
        $rows = $this->createQueryBuilder('r')
            ->Where('r.date = :date')
            ->setParameter('date', new \DateTime($date))
            ->OrderBy('r.time', 'ASC')
            ->addOrderBy('r.tableDetails', 'ASC')
            ->getQuery()
            ->getResult();

        return $rows;
    }

    public function getMonthlyReservation($date)
    {
        return $this->createQueryBuilder('r')
            ->select('count(r.id)')
            ->where('r.date >= :startDate and r.date < :endDate')
            ->getQuery()
            ->setParameter('startDate',date_create($date)->modify('first day of this month'))
            ->setParameter('endDate',date_create($date)->modify('last day of this month'))
            ->getSingleScalarResult();

    }

    public function findNonclosedReservations()
    {
        return $this->createQueryBuilder('r')
//            ->where('r.reservationDate <= :date')
            ->andWhere('r.status = :status')
            ->getQuery()
//            ->setParameter('date',new \DateTime("now", new \DateTimeZone('Europe/Warsaw')))
            ->setParameter('status','Accepted')
            ->getResult();
    }

}
