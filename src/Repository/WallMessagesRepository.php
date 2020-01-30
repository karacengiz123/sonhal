<?php

namespace App\Repository;

use App\Entity\WallMessages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * @method WallMessages|null find($id, $lockMode = null, $lockVersion = null)
 * @method WallMessages|null findOneBy(array $criteria, array $orderBy = null)
 * @method WallMessages[]    findAll()
 * @method WallMessages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WallMessagesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, WallMessages::class);
    }

    // /**
    //  * @return WallMessages[] Returns an array of WallMessages objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WallMessages
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
