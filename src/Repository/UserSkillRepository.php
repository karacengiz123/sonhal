<?php

namespace App\Repository;


use App\Entity\UserSkill;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * @method UserSkill|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserSkill|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserSkill[]    findAll()
 * @method UserSkill[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserSkillRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserSkill::class);
    }


}
