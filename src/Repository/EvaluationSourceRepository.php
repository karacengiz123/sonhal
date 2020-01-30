<?php

namespace App\Repository;

use App\Entity\EvaluationSource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * @method EvaluationSource|null find($id, $lockMode = null, $lockVersion = null)
 * @method EvaluationSource|null findOneBy(array $criteria, array $orderBy = null)
 * @method EvaluationSource[]    findAll()
 * @method EvaluationSource[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvaluationSourceRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, EvaluationSource::class);
    }
}
