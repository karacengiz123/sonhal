<?php

namespace App\Repository;

use App\Entity\EvaluationAnswer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * @method EvaluationAnswer|null find($id, $lockMode = null, $lockVersion = null)
 * @method EvaluationAnswer|null findOneBy(array $criteria, array $orderBy = null)
 * @method EvaluationAnswer[]    findAll()
 * @method EvaluationAnswer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvaluationAnswerRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, EvaluationAnswer::class);
    }
}
