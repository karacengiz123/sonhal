<?php

namespace App\Repository;

use App\Entity\EvaluationStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * @method EvaluationStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method EvaluationStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method EvaluationStatus[]    findAll()
 * @method EvaluationStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvaluationStatusRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, EvaluationStatus::class);
    }

    // /**
    //  * @return EvaluationStatus[] Returns an array of EvaluationStatus objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EvaluationStatus
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
