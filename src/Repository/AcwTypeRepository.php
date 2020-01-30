<?php

namespace App\Repository;

use App\Entity\AcwType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * @method AcwType|null find($id, $lockMode = null, $lockVersion = null)
 * @method AcwType|null findOneBy(array $criteria, array $orderBy = null)
 * @method AcwType[]    findAll()
 * @method AcwType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AcwTypeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AcwType::class);
    }

    // /**
    //  * @return AcwType[] Returns an array of AcwType objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AcwType
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
