<?php

namespace App\Repository;

use App\Entity\FormCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * @method FormCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method FormCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method FormCategory[]    findAll()
 * @method FormCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormCategoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FormCategory::class);
    }

    // /**
    //  * @return FormCategory[] Returns an array of FormCategory objects
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
    public function findOneBySomeField($value): ?FormCategory
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
