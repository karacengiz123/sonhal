<?php

namespace App\Repository;

use App\Entity\Ci2ci;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * @method Ci2ci|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ci2ci|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ci2ci[]    findAll()
 * @method Ci2ci[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class Ci2ciRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Ci2ci::class);
    }

    // /**
    //  * @return Ci2ci[] Returns an array of Ci2ci objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Ci2ci
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
