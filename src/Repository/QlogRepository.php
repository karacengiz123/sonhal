<?php

namespace App\Repository;

use App\Entity\Qlog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * @method Qlog|null find($id, $lockMode = null, $lockVersion = null)
 * @method Qlog|null findOneBy(array $criteria, array $orderBy = null)
 * @method Qlog[]    findAll()
 * @method Qlog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QlogRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Qlog::class);
    }

    // /**
    //  * @return Qlog[] Returns an array of Qlog objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Qlog
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
