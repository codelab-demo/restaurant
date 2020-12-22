<?php

namespace App\Repository;

use App\Entity\Menu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Menu|null find($id, $lockMode = null, $lockVersion = null)
 * @method Menu|null findOneBy(array $criteria, array $orderBy = null)
 * @method Menu[]    findAll()
 * @method Menu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Menu::class);
    }

    public function findSpecials() {
        return $this->createQueryBuilder('u')
            ->Where('u.special IS NOT NULL')
            ->orderBy('u.special', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getCategories() {
        return $this->createQueryBuilder('u')
            ->select('u.category')
            ->groupBy('u.category')
            ->getQuery()
            ->getArrayResult();
    }
}
