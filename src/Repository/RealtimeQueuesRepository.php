<?php

namespace App\Repository;

use App\Entity\RealtimeQueues;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * @method RealtimeQueues|null find($id, $lockMode = null, $lockVersion = null)
 * @method RealtimeQueues|null findOneBy(array $criteria, array $orderBy = null)
 * @method RealtimeQueues[]    findAll()
 * @method RealtimeQueues[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RealtimeQueuesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, RealtimeQueues::class);
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
