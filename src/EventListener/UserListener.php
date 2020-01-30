<?php
/**
 * Created by PhpStorm.
 * User: sarpdoruk
 * Date: 27.11.2018
 * Time: 12:58
 */

namespace App\EventListener;


use App\Asterisk\Entity\Extens;
use App\Entity\AgentBreak;
use App\Entity\BreakGroup;
use App\Entity\RealtimeQueueMembers;
use App\Entity\StateLog;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\UserProfile;
use App\Entity\IvrServiceLog;
use Doctrine\Migrations\Version\State;
use Doctrine\ORM\Event\LifecycleEventArgs;
use mysql_xdevapi\Exception;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Validator\Constraints\Date;

class UserListener
{
    /**
     * @param User $user
     * @param LifecycleEventArgs $args
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function postUpdate(User $user, LifecycleEventArgs $args)
    {

        $em = $args->getEntityManager();
        $preEndTime = new \DateTime();
        $preStateLog = $em->getRepository(StateLog::class)
            ->findOneBy(['user' => $user, 'endTime' => null]);
        $preState = $user->getState();
        if ($preStateLog instanceof StateLog) {

            $preState = $preStateLog->getState();

            $duration = $preEndTime->getTimestamp() - $preStateLog->getStartTime()->getTimestamp();
            if ($preState != $user->getState()) {
                $preStateLog->setEndTime($preEndTime);
            }
            $preStateLog->setDuration($duration);

            $em->persist($preStateLog);
            $em->flush();


        }

        if (!$preStateLog instanceof StateLog || $preState != $user->getState()) {
            $state = new StateLog();
            $state->setUser($user)
                ->setState($user->getState())
                ->setStartTime($preEndTime)
                ->setDuration(0);

            $em->persist($state);
            $em->flush();
        }
    }
}