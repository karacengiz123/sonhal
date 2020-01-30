<?php

namespace App\Repository;

use App\Entity\EvaluationExtraSource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * @method EvaluationExtraSource|null find($id, $lockMode = null, $lockVersion = null)
 * @method EvaluationExtraSource|null findOneBy(array $criteria, array $orderBy = null)
 * @method EvaluationExtraSource[]    findAll()
 * @method EvaluationExtraSource[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvaluationExtraSourceRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, EvaluationExtraSource::class);
    }

    // /**
    //  * @return EvaluationExtraSource[] Returns an array of EvaluationExtraSource objects
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
    public function findOneBySomeField($value): ?EvaluationExtraSource
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
