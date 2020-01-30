<?php

namespace App\Repository;

use App\Entity\FormQuestionOption;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * @method FormQuestionOption|null find($id, $lockMode = null, $lockVersion = null)
 * @method FormQuestionOption|null findOneBy(array $criteria, array $orderBy = null)
 * @method FormQuestionOption[]    findAll()
 * @method FormQuestionOption[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormAnswerRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FormQuestionOption::class);
    }

    // /**
    //  * @return FormAnswer[] Returns an array of FormAnswer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FormAnswer
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
