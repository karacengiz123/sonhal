<?php

namespace App\Repository;

use App\Asterisk\Entity\Extens;
use App\Entity\AcwLog;
use App\Entity\AcwType;
use App\Entity\AgentBreak;
use App\Entity\BreakType;
use App\Entity\LoginLog;
use App\Entity\Orders;
use App\Entity\RealtimeQueueMembers;
use App\Entity\RegisterLog;
use App\Entity\User;
use App\Entity\UserSkill;
use App\Helpers\Date;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param User $user
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function closeOpenBreaks(User $user)
    {
        $em = $this->getEntityManager();

        $userAcwLogs = $em->getRepository(AcwLog::class)->findBy(["user" => $user, "duration" => 0, "endTime" => null]);

        if (count($userAcwLogs) > 0) {
            /**
             * @var AcwLog $acwLog
             */
            foreach ($userAcwLogs as $acwLog) {

                $nowDate = new \DateTime();

                $durDiff = $nowDate->getTimestamp() - $acwLog->getStartTime()->getTimestamp();

                if ($acwLog->getAcwType()->getId() == 1 and $durDiff > 20) {
                    $duration = 20;
                    $endTime = $acwLog->getStartTime();
                    $endDate = $endTime->modify($duration . " sec");
                } elseif ($acwLog->getAcwType()->getId() == 1 and $durDiff <= 20) {
                    $duration = $durDiff;
                    $endTime = $acwLog->getStartTime();
                    $endDate = $endTime->modify($duration . " sec");
                } elseif ($acwLog->getAcwType()->getId() == 3 and $durDiff > 20) {
                    $duration = 20;
                    $endTime = $acwLog->getStartTime();
                    $endDate = $endTime->modify($duration . " sec");
                } elseif ($acwLog->getAcwType()->getId() == 3 and $durDiff <= 20) {
                    $duration = $durDiff;
                    $endTime = $acwLog->getStartTime();
                    $endDate = $endTime->modify($duration . " sec");
                } else {
                    $duration = $durDiff;
                    $endTime = $acwLog->getStartTime();
                    $endDate = $endTime->modify($duration . " sec");
                }
                $acwLog
                    ->setEndTime($endDate)
                    ->setDuration($duration ?: 1);
                $em->persist($acwLog);
                $em->flush();
            }
        }

        $userBreaks = $em->getRepository(AgentBreak::class)->findBy(["user" => $user, "duration" => 0, "endTime" => null]);

        if (count($userBreaks) > 0) {
            /**
             * @var AgentBreak $userBreak
             */
            foreach ($userBreaks as $userBreak) {

                $nowDate = new \DateTime();

                $durDiff = $nowDate->getTimestamp() - $userBreak->getStartTime()->getTimestamp();

                $userBreak
                    ->setEndTime($nowDate)
                    ->setDuration($durDiff ?: 1);
                $em->persist($userBreak);
                $em->flush();
            }
        }
    }

    /**
     * @param User $user
     * @return array | bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function applyOrder(User $user)
    {
        $em = $this->getEntityManager();
        $orders = $em->getRepository(Orders::class)->findBy(['user' => $user]);
        $entity = null;
        if (count($orders) > 0) {
            /**
             * @var Orders $order
             */
            foreach ($orders as $order) {
                $this->closeOpenBreaks($user);
                if ($order->getStartOrStop() == 0) {
                    $name = "Hazır";
                    $state = 1;
                    $type = "Hazır";

                    $em->remove($order);
                    $em->flush();
                    continue;
                }
                if ($order->getType() == "AcwLog") {
                    $entity = new AcwLog();
                    $type = $em->find(AcwType::class, $order->getSubType());
                    $entity
                        ->setUser($user)
                        ->setAcwType($type)
                        ->setStartTime(new \DateTime())
                        ->setDuration(0);
                    $state = $type->getState();
                } elseif ($order->getType() == "AgentBreak") {
                    $entity = new AgentBreak();
                    $type = $em->find(BreakType::class, $order->getSubType());
                    $entity->setUser($user)
                        ->setBreakType($type)
                        ->setStartTime(new \DateTime())
                        ->setDuration(0);
                    $state = 4;
                }
                if ($entity) {
                    $em->persist($entity);
                    $em->flush();
                    $name = $type->getName();
                }
                $em->remove($order);
                $em->flush();
                $orderType = $order->getType();
            }

            if ($type) {
                return ['name' => $name, 'state' => $state];
            }
        }

        return false;
    }

    /**
     * @param User $user
     * @param int $state
     * @param $endStartTime
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function setState(User $user, int $state)
    {

        $em = $this->getEntityManager();
        $user
            ->setState($state)
            ->setLastStateChange(new \DateTime());
        $em->persist($user);
        $em->flush();

        $rqm = $em->getRepository(RealtimeQueueMembers::class);

        $rqm->createQueryBuilder('realtime_queue_members')
            ->update()
            ->set('realtime_queue_members.paused', ":paused")
            ->where('realtime_queue_members.user = :user')
            ->setParameters([
                "user" => $user,
                "paused" => $state == 1 ? 0 : 1,
            ])->getQuery()->execute();
    }

    /**
     * @param User $user
     * @param int $state
     * @param $endStartTime
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function setHomeState(User $user, int $state)
    {
        $em = $this->getEntityManager();
        $user
            ->setState($state)
            ->setLastStateChange(new \DateTime());
        $em->persist($user);
        $em->flush();
    }


    /**
     * @param User $user
     * @param int $state
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function setChatState(User $user, int $state)
    {
        $em = $this->getEntityManager();
        $user
            ->setChatStatus($state)
            ->setChatLastActivity(new \DateTime());
        $em->persist($user);
        $em->flush();
        return true;
    }


    /**
     * @param User $user
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function loginUser(User $user): bool
    {
        $em = $this->getEntityManager();
        $userRepository = $em->getRepository(User::class);
        $loginLogRepository = $em->getRepository(LoginLog::class);
        $userSkillRepository = $em->getRepository(UserSkill::class);

        $loginLog = $loginLogRepository->findOneBy(["userId" => $user, "EndTime" => null]);

        if (is_null($loginLog)) {
            $loginLog = new LoginLog();

            $loginLog
                ->setUserId($user)
                ->setStartTime(new \DateTime())
                ->setLastOnline(new \DateTime())
                ->setDuration(0);
            $em->persist($loginLog);
            $em->flush();
        } else {
            $loginLog->setEndTime(new \DateTime());
            $em->persist($loginLog);
            $em->flush();

            $loginLog2 = new LoginLog();

            $loginLog2
                ->setUserId($user)
                ->setStartTime(new \DateTime())
                ->setLastOnline(new \DateTime())
                ->setDuration(0);

            $em->persist($loginLog2);
            $em->flush();
        }

        $extension = $user->getUserProfile()->getExtension();
        $userSkills = $userSkillRepository->findBy(["member" => $extension]);
        if (count($userSkills) > 0) {
            $rqm = $em->getRepository(RealtimeQueueMembers::class);
            $rqm->createQueryBuilder('realtime_queue_members')
                ->delete()
                ->where('realtime_queue_members.user = :user')
                ->setParameter("user", $user)->getQuery()->execute();
            /**
             * @var UserSkill $userSkill
             */
            foreach ($userSkills as $userSkill) {
                $rtqm = new RealtimeQueueMembers();
                $rtqm
                    ->setQueueName($userSkill->getQueue())
                    ->setUser($user)
                    ->setInterface("Local/" . $userSkill->getMember() . "@from-internal-hosted/n")
                    ->setMembername($userSkill->getMember())
                    ->setStateInterface("PJSIP/" . $userSkill->getMember())
                    ->setPenalty($userSkill->getPenalty())
                    ->setPaused(1);
                $em->persist($rtqm);
                $em->flush();
            }
        }

        $extens = $em->getRepository(Extens::class);
        $extens->createQueryBuilder("e")
            ->update()
            ->set("e.user", ":setUser")
            ->where("e.user=:user")
            ->setParameters([
                "user" => $user,
                "setUser" => null
            ])->getQuery()->execute();

        $extens = $em->getRepository(Extens::class);
        $extens->createQueryBuilder("e")
            ->update()
            ->set("e.user", ":setUser")
            ->where("e.exten=:exten")
            ->setParameters([
                "exten" => $extension,
                "setUser" => $user
            ])->getQuery()->execute();

        $userRepository->setState($user, 0);

        return true;
    }


    /**
     * @param User $user
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function logoutUser(User $user): bool
    {

        $em = $this->getEntityManager();
        $userRepository = $em->getRepository(User::class);
        $loginLogRepository = $em->getRepository(LoginLog::class);
        $registerLogRepository = $em->getRepository(RegisterLog::class);

        $userRepository->closeOpenBreaks($user);
        $nowTime = new \DateTime();

        $loginLog = $loginLogRepository->findOneBy(["userId" => $user, "EndTime" => null]);
        if (!is_null($loginLog)) {
            $duration = $loginLog->getLastOnline()->getTimestamp() - $loginLog->getStartTime()->getTimestamp();
            $loginLog
                ->setEndTime(new \DateTime())
                ->setLastOnline(new \DateTime())
                ->setDuration($duration);
            $em->persist($loginLog);
            $em->flush();
        }

        $registerLog = $registerLogRepository->findOneBy(["user" => $user, "EndTime" => null]);
        if (!is_null($registerLog)) {
            $lastOnline = $registerLog->getLastRegister();
            $duration = $nowTime->getTimestamp() - $registerLog->getStartTime()->getTimestamp();
            $registerLog
                ->setEndTime($lastOnline)
                ->setLastRegister($lastOnline)
                ->setDuration($duration);
            $em->persist($registerLog);
            $em->flush();
        }

        $extens = $em->getRepository(Extens::class);
        $extens->createQueryBuilder("e")
            ->update()
            ->set("e.user", ":setUser")
            ->where("e.user=:user")
            ->setParameters([
                "user" => $user,
                "setUser" => null
            ])->getQuery()->execute();

        $rqm = $em->getRepository(RealtimeQueueMembers::class);
        $rqm->createQueryBuilder('realtime_queue_members')
            ->delete()
            ->where('realtime_queue_members.user = :user')
            ->setParameter("user", $user)->getQuery()->execute();


        $userRepository->setState($user, 0);
        $userRepository->setChatState($user, 0);

        return true;
    }
}