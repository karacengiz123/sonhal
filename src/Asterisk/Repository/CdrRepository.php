<?php

namespace App\Asterisk\Repository;

use App\Asterisk\Entity\Cdr;
use App\Asterisk\Entity\QueueLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;


class CdrRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Cdr::class);
    }

    /**
     * @param string $callId
     * @return string|null
     */
    public function getUserFieldByCallId(string $callId) :?string
    {

        $userField = null;
        try {
            $em = $this->getEntityManager();
            $xcallid = $em->getRepository(QueueLog::class)->createQueryBuilder("ql")
                ->select("ql.data1")
                ->where("ql.callid=:callid")
                ->andWhere("ql.event=:event")
                ->setParameters([
                    "callid" => $callId,
                    "event" => 'X-CALLID'
                ])->setMaxResults(1)->getQuery()->getSingleScalarResult();

            $userField = $this->createQueryBuilder('cdr')
                ->select("cdr.userfield")
                ->where("cdr.callId=:callId")
                ->andWhere("cdr.lastapp=:lastapp")
                ->setParameters([
                    "callId" => $xcallid,
                    "lastapp" => "Dial"
                ])->getQuery()->getSingleScalarResult();

            if (substr($userField, 0, 1) == ";") $userField = substr($userField, 1, strlen($userField));
        } catch (NoResultException $exception) {

        }

        return $userField;
    }
}
