<?php

namespace App\Repository;

use App\Entity\FormTemplate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * @method FormTemplate|null find($id, $lockMode = null, $lockVersion = null)
 * @method FormTemplate|null findOneBy(array $criteria, array $orderBy = null)
 * @method FormTemplate[]    findAll()
 * @method FormTemplate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormTemplateRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FormTemplate::class);
    }

    // /**
    //  * @return QFormTemplate[] Returns an array of QFormTemplate objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?QFormTemplate
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
