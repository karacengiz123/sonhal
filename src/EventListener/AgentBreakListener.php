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
use App\Entity\Team;
use App\Entity\User;
use App\Entity\UserProfile;
use App\Entity\IvrServiceLog;
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

class AgentBreakListener
{
    /**
     * @var TokenStorage
     * @var Container
     */
    private $tokenStorage;
    private $container;

    public function __construct(TokenStorage $tokenStorage, Container $container)
    {
        $this->tokenStorage = $tokenStorage;
        $this->container = $container;
    }

    /**
     * @param AgentBreak $agentBreak
     * @param LifecycleEventArgs $args
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function prePersist(AgentBreak $agentBreak, LifecycleEventArgs $args)
    {

        if ($agentBreak->getUser() == ""){
            $user = $this->tokenStorage->getToken()->getUser();
        }else{
            $user = $agentBreak->getUser();
        }

        /**
         * @var BreakGroup $breakGroup
         * @var User $user
         */
        $breakGroup = $user->getBreakGroup();

        $breakedUserCount = $args->getEntityManager()->getRepository('Main:AgentBreak')
            ->createQueryBuilder('ab')
            ->select('count(ab.id)')
            ->leftJoin('ab.user', 'u')
            ->where('u.breakGroup =:breakGroup')
            ->setParameter('breakGroup', $breakGroup->getId())
            ->andWhere("ab.endTime IS NULL")
            ->getQuery()->getSingleScalarResult();

        if($breakGroup->getBreakLimit() > $breakedUserCount) {
            /**
             * @var Extens $exten
             */
            $exten = $args->getEntityManager()->getRepository(Extens::class)->findOneBy(["user"=>$user]);

            if (!is_null($exten)){
                if ($exten->getExten() == $user->getUserProfile()->getExtension()){
                    $extension = $user->getUserProfile()->getExtension();
                }else{
                    $extension = $exten->getExten();
                }
            }else{
                $extension = $user->getUserProfile()->getExtension();
            }

            $rqm = $args->getEntityManager()->getRepository(RealtimeQueueMembers::class);
            $rqm->createQueryBuilder('realtime_queue_members')
                ->update()
                ->set('realtime_queue_members.paused', ":paused")
                ->where('realtime_queue_members.membername = :exten')
                ->setParameters([
                    "exten"=>$extension,
                    "paused"=>1,
                ])->getQuery()->execute();

            $agentBreak->setUser($user);

            return new JsonResponse(['status' => 1,'response'=>1]);
        } else {
            throw new \Exception("Mola limiti dolu lütfen daha sonra tekrar deneyiniz.");
        }
    }
}