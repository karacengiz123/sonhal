<?php
/**
 * Created by PhpStorm.
 * User: aydn33
 * Date: 19.02.2019
 * Time: 09:55
 */

namespace App\Controller;


use App\Asterisk\Entity\Extens;
use App\Entity\AcwLog;
use App\Entity\AgentBreak;
use App\Entity\BreakType;
use App\Entity\Calls;
use App\Entity\RealtimeQueueMembers;
use App\Entity\User;
use App\Entity\UserProfile;
use App\Helpers\Date;
use App\Repository\AgentBreakRepository;
use App\Repository\UserRepository;
use r\Queries\Control\Js;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/api")
 * Class AgentBreakController
 * @package App\Controller
 */
class AgentBreakController extends AbstractController
{
    /**
     * @Route("/softphone/breakStart/{breakType}", name="api_softphone_breakStart")
     * @param UserInterface $user
     * @param UserRepository $userRepository
     * @param BreakType $breakType
     * @return JsonResponse
     * @throws \Exception
     */
    public function breakStart(UserInterface $user,UserRepository $userRepository, BreakType $breakType)
    {
        $em = $this->getDoctrine()->getManager();
        /**
         * @var User $user
         */
        $userRepository->closeOpenBreaks($user);

        $apply = $userRepository->applyOrder($user);
        if (is_array($apply)) {
            $state = $apply['state'];
            $text = $apply['name'];
        } else {
            $agentBreak = new AgentBreak();
            $agentBreak
                ->setUser($user)
                ->setDuration(0)
                ->setStartTime(new \DateTime())
                ->setBreakType($breakType);

            $em->persist($agentBreak);
            $em->flush();

            $text = $breakType->getName();
            $state = 4;
        }
        $userRepository->setState($user, $state);
        return new JsonResponse(["state" => $state, "text" => $text]);
    }

    /**
     * @Route("/softphone/breakStop", name="softPhoneBreakStop")
     * @param UserInterface $user
     * @param UserRepository $userRepository
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function breakStop(UserInterface $user, UserRepository $userRepository)
    {
        $em = $this->getDoctrine()->getManager();
        /**
         * @var User $user
         */
        $userRepository->closeOpenBreaks($user);

        $apply = $userRepository->applyOrder($user);
        if (is_array($apply)) {
            $state = 4;
            $text = $apply['name'];
        } else {
            $state = 1;
            $text = "Hazır";
        }
        $userRepository->setState($user, $state);

        return new JsonResponse(["state" => $state, "text" => $text]);
    }


    /**
     * @Route("/agent-break-control", name="agent_break_control")
     * @param UserInterface $user
     * @param Request $request
     * @param AgentBreakRepository $agentBreakRepository
     * @return JsonResponse
     * @throws \Exception
     */
    public function agentBreakControl(UserInterface $user, Request $request, AgentBreakRepository $agentBreakRepository)
    {
        $otherUser = $request->get('otherUser');
        if(isset($otherUser)) {
            $user = $request->get('otherUser');
        }

        $em = $this->getDoctrine()->getManager();
        $agentBreakContol = $agentBreakRepository->findOneBy(["user"=>$user,"endTime"=>null,"duration"=>0]);

        if (is_null($agentBreakContol)){
            return new JsonResponse(["agentBreakCount"=>0,"agentBreak"=>null]);
        }else{
            $nowTime = new \DateTime();

            $timeDifference = $nowTime->getTimestamp() - $agentBreakContol->getStartTime()->getTimestamp();

            return new JsonResponse(["agentBreakCount"=>1,"agentBreakTimer"=>$timeDifference,"breakTypeName"=>$agentBreakContol->getBreakType()->getName()]);
        }

    }

    /**
     * @Route("/oudbound-call-pauser", name="oudbound_call_pauser")
     * @param UserInterface $user
     * @param UserRepository $userRepository
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function ounboundCallPauser(UserInterface $user, UserRepository $userRepository)
    {
        $em = $this->getDoctrine()->getManager();
        /**
         * @var User $user
         */
        $extension = $user->getUserProfile()->getExtension();

        $userRepository->closeOpenBreaks($user);

        $rqm = $em->getRepository(RealtimeQueueMembers::class);
        $rqm->createQueryBuilder('realtime_queue_members')
            ->update()
            ->set('realtime_queue_members.paused', ":paused")
            ->set('realtime_queue_members.pauseTypeTable', ":ptype")
            ->set('realtime_queue_members.endBreakTime',':endTime')
            ->where('realtime_queue_members.membername = :exten')
            ->setParameters([
                "exten" => $extension,
                "paused" => 1,
            ])->getQuery()->execute();

        return new JsonResponse(['status' => 1]);
    }

    /**
     * @Route("/inbound-call-pauser", name="inbound_call_pauser")
     * @param UserInterface $user
     * @param UserRepository $userRepository
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function inboundCallPauser(UserInterface $user,UserRepository $userRepository)
    {
        $em = $this->getDoctrine()->getManager();
        /**
         * @var User $user
         */
        $extension = $user->getUserProfile()->getExtension();

        $userRepository->closeOpenBreaks($user);

        $rqm = $em->getRepository(RealtimeQueueMembers::class);
        $rqm->createQueryBuilder('realtime_queue_members')
            ->update()
            ->set('realtime_queue_members.paused', ":paused")
            ->set('realtime_queue_members.pauseTypeTable',":ptype")
            ->set('realtime_queue_members.endBreakTime',':endTime')
            ->where('realtime_queue_members.membername = :exten')
            ->setParameters([
                "exten"=>$extension,
                "paused"=>1,
            ])->getQuery()->execute();

        return new JsonResponse(['status' => 1]);
    }
}