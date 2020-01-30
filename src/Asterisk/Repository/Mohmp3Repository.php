<?php

namespace App\Asterisk\Repository;

use App\Asterisk\Entity\Mohmp3;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;


class Mohmp3Repository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Mohmp3::class);
    }
}
