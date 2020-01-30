<?php

namespace App\Repository;

use App\Entity\WallMessage2;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * @method WallMessage2|null find($id, $lockMode = null, $lockVersion = null)
 * @method WallMessage2|null findOneBy(array $criteria, array $orderBy = null)
 * @method WallMessage2[]    findAll()
 * @method WallMessage2[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WallMessage2Repository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, WallMessage2::class);
    }

    // /**
    //  * @return WallMessage2[] Returns an array of WallMessage2 objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WallMessage2
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
