<?php

namespace App\Asterisk\Repository;

use App\Asterisk\Entity\Phonebook;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;


class PhonebookRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Phonebook::class);
    }
}
