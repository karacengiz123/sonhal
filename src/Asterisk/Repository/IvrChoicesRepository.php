<?php

namespace App\Asterisk\Repository;

use App\Asterisk\Entity\Agents;
use App\Asterisk\Entity\IvrChoices;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;


class IvrChoicesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, IvrChoices::class);
    }
}
