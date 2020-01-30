<?php

namespace App\Repository;

use App\Entity\IvrServiceLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * @method IvrServiceLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method IvrServiceLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method IvrServiceLog[]    findAll()
 * @method IvrServiceLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IvrServiceLogRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, IvrServiceLog::class);
    }

    // /**
    //  * @return IvrServiceLog[] Returns an array of IvrServiceLog objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?IvrServiceLog
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
