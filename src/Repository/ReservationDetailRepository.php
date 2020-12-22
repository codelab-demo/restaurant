<?php

namespace App\Repository;

use App\Entity\Reservation;
use App\Entity\ReservationDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReservationDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReservationDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReservationDetail[]    findAll()
 * @method ReservationDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReservationDetail::class);
    }

    public function getMonthlyearnings($date)
    {

        $sum = $this->createQueryBuilder('r')
            ->join('r.reservationId', 'x')
            ->select('SUM(r.price)')
            ->where('x.date  >= :startDate and x.date < :endDate')
            ->setParameter('startDate',date_create($date)->modify('first day of this month'))
            ->setParameter('endDate',date_create($date)->modify('last day of this month'))
            ->getQuery()
            ->getSingleScalarResult();

        return $sum;


    }

}
