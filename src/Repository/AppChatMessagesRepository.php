<?php

namespace App\Repository;

use App\Entity\AppChatMessages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * @method AppChatMessages|null find($id, $lockMode = null, $lockVersion = null)
 * @method AppChatMessages|null findOneBy(array $criteria, array $orderBy = null)
 * @method AppChatMessages[]    findAll()
 * @method AppChatMessages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppChatMessagesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AppChatMessages::class);
    }

    // /**
    //  * @return AppChatMessages[] Returns an array of AppChatMessages objects
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
    public function findOneBySomeField($value): ?AppChatMessages
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
