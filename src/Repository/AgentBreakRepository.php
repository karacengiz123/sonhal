<?php

namespace App\Repository;

use App\Entity\AgentBreak;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * @method AgentBreak|null find($id, $lockMode = null, $lockVersion = null)
 * @method AgentBreak|null findOneBy(array $criteria, array $orderBy = null)
 * @method AgentBreak[]    findAll()
 * @method AgentBreak[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AgentBreakRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AgentBreak::class);
    }

}
