<?php

namespace App\Repository;

use App\Entity\RecordListenLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * @method RecordListenLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method RecordListenLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method RecordListenLog[]    findAll()
 * @method RecordListenLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecordListenLogRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, RecordListenLog::class);
    }

    // /**
    //  * @return RecordListenLog[] Returns an array of RecordListenLog objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RecordListenLog
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
