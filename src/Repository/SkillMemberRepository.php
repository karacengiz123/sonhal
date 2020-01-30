<?php

namespace App\Repository;

use App\Entity\SkillMember;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * @method SkillMember|null find($id, $lockMode = null, $lockVersion = null)
 * @method SkillMember|null findOneBy(array $criteria, array $orderBy = null)
 * @method SkillMember[]    findAll()
 * @method SkillMember[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SkillMemberRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SkillMember::class);
    }

    // /**
    //  * @return SkillMember[] Returns an array of SkillMember objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SkillMember
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
