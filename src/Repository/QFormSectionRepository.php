<?php

namespace App\Repository;

use App\Entity\QFormSection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * @method QFormSection|null find($id, $lockMode = null, $lockVersion = null)
 * @method QFormSection|null findOneBy(array $criteria, array $orderBy = null)
 * @method QFormSection[]    findAll()
 * @method QFormSection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QFormSectionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, QFormSection::class);
    }

    // /**
    //  * @return QFormSection[] Returns an array of QFormSection objects
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
    public function findOneBySomeField($value): ?QFormSection
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
