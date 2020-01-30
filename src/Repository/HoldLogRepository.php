<?php

namespace App\Repository;

use App\Entity\HoldLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * @method HoldLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method HoldLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method HoldLog[]    findAll()
 * @method HoldLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HoldLogRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, HoldLog::class);
    }

    // /**
    //  * @return AcwLog[] Returns an array of AcwLog objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AcwLog
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
