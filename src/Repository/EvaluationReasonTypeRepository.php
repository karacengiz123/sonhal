<?php

namespace App\Repository;

use App\Entity\EvaluationReasonType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * @method EvaluationReasonType|null find($id, $lockMode = null, $lockVersion = null)
 * @method EvaluationReasonType|null findOneBy(array $criteria, array $orderBy = null)
 * @method EvaluationReasonType[]    findAll()
 * @method EvaluationReasonType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvaluationReasonTypeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, EvaluationReasonType::class);
    }

    // /**
    //  * @return EvaluationReasonType[] Returns an array of EvaluationReasonType objects
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
    public function findOneBySomeField($value): ?EvaluationReasonType
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
