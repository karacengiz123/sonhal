<?php

namespace App\Asterisk\Repository;

use App\Asterisk\Entity\InboundRoutes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;


class InboundRoutesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, InboundRoutes::class);
    }
}
