<?php

namespace App\Repository;

use App\Entity\QueuesMembersDynamic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * @method QueuesMembersDynamic|null find($id, $lockMode = null, $lockVersion = null)
 * @method QueuesMembersDynamic|null findOneBy(array $criteria, array $orderBy = null)
 * @method QueuesMembersDynamic[]    findAll()
 * @method QueuesMembersDynamic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QueuesMembersDynamicRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, QueuesMembersDynamic::class);
    }

    // /**
    //  * @return QueuesMembersDynamic[] Returns an array of QueuesMembersDynamic objects
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
    public function findOneBySomeField($value): ?QueuesMembersDynamic
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
