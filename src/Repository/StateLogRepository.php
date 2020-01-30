<?php

namespace App\Repository;

use App\Entity\StateLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * @method StateLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method StateLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method StateLog[]    findAll()
 * @method StateLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StateLogRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, StateLog::class);
    }

    // /**
    //  * @return StateLog[] Returns an array of StateLog objects
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
    public function findOneBySomeField($value): ?StateLog
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
