<?php

namespace App\Repository;

use App\Entity\FormSection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * @method FormSection|null find($id, $lockMode = null, $lockVersion = null)
 * @method FormSection|null findOneBy(array $criteria, array $orderBy = null)
 * @method FormSection[]    findAll()
 * @method FormSection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormSectionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FormSection::class);
    }

    // /**
    //  * @return FormSection[] Returns an array of FormSection objects
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
    public function findOneBySomeField($value): ?FormSection
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
