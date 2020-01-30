<?php

namespace App\Repository;

use App\Entity\GuideGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * @method GuideGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method GuideGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method GuideGroup[]    findAll()
 * @method GuideGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GuideGroupRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, GuideGroup::class);
    }

    // /**
    //  * @return GuideGroup[] Returns an array of GuideGroup objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GuideGroup
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
