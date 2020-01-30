<?php

namespace App\Repository;

use App\Entity\BreakType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * @method BreakType|null find($id, $lockMode = null, $lockVersion = null)
 * @method BreakType|null findOneBy(array $criteria, array $orderBy = null)
 * @method BreakType[]    findAll()
 * @method BreakType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BreakTypeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BreakType::class);
    }
}
