<?php

namespace App\Repository;

use App\Entity\BreakGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * @method BreakGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method BreakGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method BreakGroup[]    findAll()
 * @method BreakGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BreakGroupRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BreakGroup::class);
    }

    // /**
    //  * @return BreakGroup[] Returns an array of BreakGroup objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BreakGroup
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
