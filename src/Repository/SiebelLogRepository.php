<?php

namespace App\Repository;

use App\Entity\SiebelLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * @method SiebelLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method SiebelLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method SiebelLog[]    findAll()
 * @method SiebelLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SiebelLogRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SiebelLog::class);
    }

    // /**
    //  * @return SiebelLog[] Returns an array of SiebelLog objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SiebelLog
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
