<?php

namespace App\Asterisk\Repository;

use App\Asterisk\Entity\CustomDialplan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;


class CustomDialplanRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CustomDialplan::class);
    }
}
