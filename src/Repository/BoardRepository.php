<?php

namespace App\Repository;

use App\Entity\Board;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Board|null find($id, $lockMode = null, $lockVersion = null)
 * @method Board|null findOneBy(array $criteria, array $orderBy = null)
 * @method Board[]    findAll()
 * @method Board[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BoardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Board::class);
    }

    public function totalTables(): int
    {
        return $this->createQueryBuilder('a')
            ->select('count(a.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findTables($date, $time) {
        return $this->createQueryBuilder('board')
            ->select('board.name, case when reservations.date = :date and reservations.time = :time then \'no\' else \'yes\' end as free')
            ->leftJoin('board.reservations', 'reservations')
            ->getQuery()
            ->setParameter('time' ,new \DateTime($time))
            ->setParameter('date' ,new \DateTime($date))
            ->getResult();
    }

}
