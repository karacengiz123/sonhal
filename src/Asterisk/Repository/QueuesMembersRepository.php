<?php

namespace App\Asterisk\Repository;

use App\Asterisk\Entity\QueuesMembers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;


class QueuesMembersRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, QueuesMembers::class);
    }
}
