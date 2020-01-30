<?php

namespace App\Repository;

use App\Entity\RealtimeQueueMembers;
use App\Helpers\Date;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Ramsey\Uuid\Uuid;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * @method RealtimeQueueMembers|null find($id, $lockMode = null, $lockVersion = null)
 * @method RealtimeQueueMembers|null findOneBy(array $criteria, array $orderBy = null)
 * @method RealtimeQueueMembers[]    findAll()
 * @method RealtimeQueueMembers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RealtimeQueueMembersRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, RealtimeQueueMembers::class);
    }

    /**
     * @param string $exten
     * @param $paused
     * @param $type
     * @return int|null
     * @throws \Exception
     */
    public function updateExtens(string $exten,$paused,$type): ?int
    {
//       $result = $this->createQueryBuilder('realtime_queue_members')
//            ->update()
//            ->set('realtime_queue_members.paused', $paused)
//            ->set('realtime_queue_members.uniqueid', Uuid::uuid4()->toString())
//            ->set('realtime_queue_members.pauseTypeTable',$type)
//            ->where('realtime_queue_members.stateInterface = :exten')
//            ->setParameter('exten',$exten);
//
//       dump($result);
//       die('deneme');





        return null;
    }

    /**
     * @param string $exten
     * @return int|null
     */
    public function removeExtens(string $exten): ?int
    {
//        return $this->createQueryBuilder("realtime_queue_members")->delete()->where("realtime_queue_members.interface=:exten")->setParameter("exten",$exten);
        return null;
    }
}
