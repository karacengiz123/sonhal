<?php

namespace App\Asterisk\Repository;

use App\Asterisk\Entity\CustomShortcuts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;


class CustomShortcutsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CustomShortcuts::class);
    }
}
