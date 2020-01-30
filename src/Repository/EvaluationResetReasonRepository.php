<?php

namespace App\Repository;

use App\Entity\EvaluationResetReason;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * @method EvaluationResetReason|null find($id, $lockMode = null, $lockVersion = null)
 * @method EvaluationResetReason|null findOneBy(array $criteria, array $orderBy = null)
 * @method EvaluationResetReason[]    findAll()
 * @method EvaluationResetReason[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvaluationResetReasonRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, EvaluationResetReason::class);
    }

    // /**
    //  * @return EvaluationResetReason[] Returns an array of EvaluationResetReason objects
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
    public function findOneBySomeField($value): ?EvaluationResetReason
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
