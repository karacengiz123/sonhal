<?php

namespace App\Repository;

use App\Entity\FormQuestion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * @method FormQuestion|null find($id, $lockMode = null, $lockVersion = null)
 * @method FormQuestion|null findOneBy(array $criteria, array $orderBy = null)
 * @method FormQuestion[]    findAll()
 * @method FormQuestion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormQuestionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FormQuestion::class);
    }

    // /**
    //  * @return FormQuestion[] Returns an array of FormQuestion objects
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
    public function findOneBySomeField($value): ?FormQuestion
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
