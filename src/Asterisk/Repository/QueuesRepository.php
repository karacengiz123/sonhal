<?php

namespace App\Asterisk\Repository;

use App\Asterisk\Entity\Queues;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;


class QueuesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Queues::class);
    }


    /**
     * @param QueuesRepository $queuesRepository
     * @return array
     */
    public function getQueueAllName(QueuesRepository $queuesRepository) : array
    {
        $queueName=[];

        $queues=$queuesRepository->findAll();

        foreach ($queues as $queue)
        {
            $queueName[$queue->getQueue()]= $queue->getDescription();
        }

        return $queueName;

    }

}
