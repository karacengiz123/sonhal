<?php

namespace App\Repository;

use App\Entity\RegisterLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * @method RegisterLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method RegisterLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method RegisterLog[]    findAll()
 * @method RegisterLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RegisterLogRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, RegisterLog::class);
    }

    // /**
    //  * @return LoginLog[] Returns an array of LoginLog objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LoginLog
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
