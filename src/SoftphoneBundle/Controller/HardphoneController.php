<?php

namespace App\SoftphoneBundle\Controller;

use App\Asterisk\Entity\Config;
use App\Asterisk\Entity\PsContacts;
use App\Asterisk\Entity\Queues;
use App\Asterisk\Entity\Ivr;
use App\Entity\AcwLog;
use App\Entity\AcwType;
use App\Entity\AgentBreak;
use App\Entity\BreakType;
use App\Entity\Calls;
use App\Entity\Ci2ci;
use App\Entity\Guide;

use App\Entity\HoldLog;
use App\Entity\RealtimeQueueMembers;
use App\Entity\RegisterLog;
use App\Entity\User;
use App\Entity\UserSkill;
use App\Form\AcwTypeType;
use App\Helpers\Date;
use App\Services\SipServerService;
use Grpc\Call;
use function GuzzleHttp\Promise\queue;
use phpDocumentor\Reflection\DocBlock\Description;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class HardphoneController extends AbstractController
{

    public function toJson($data)
    {
        return $this->container->get('serializer')->serialize($data, 'json');
    }

    /**
     * @Route("/hardphone", name="index_hardphone")
     * @param UserInterface $user
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function indexHardphoneAction(UserInterface $user)
    {
        $em = $this->getDoctrine()->getManager();

        $queueList = [];
        $queues = $this->getDoctrine()->getManager('asterisk')->getRepository(Queues::class)->findAll();
        foreach ($queues as $queue){
            $queueList[$queue->getQueue()]=$queue->getDescription();
        }

        $ivrList2 = [];
        $ivrLists = $this->getDoctrine()->getManager('asterisk')->getRepository(Ivr::class)
            ->createQueryBuilder('i')->orderBy("i.description","ASC")->getQuery()->getResult();

        foreach ($ivrLists as $ivrList){
            if ($ivrList->getTitle() != ""){
                $ivrList2 [0] = "Ivr Listesi Seçiniz";
                $ivrList2 [$ivrList->getIdx()] = $ivrList->getTitle() ." - ". $ivrList->getDescription();
            }
        }

        $breakTypesList = $this->getDoctrine()->getManager()->getRepository(BreakType::class)->findAll();
        $acwypesList = $this->getDoctrine()->getManager()->getRepository(AcwType::class)->findByState(5);


        $reloadStateResult = $this->state($user);
        $reloadStateResult = json_decode($reloadStateResult->getContent());
        $reloadState = $reloadStateResult->state;
        $reloadStateText = $reloadStateResult->text;

        $agentOnlineControl = $this->getDoctrine()->getRepository(PsContacts::class)->findOneBy(["endpoint"=>$user->getUserProfile()->getExtension()]);
        if (is_null($agentOnlineControl)){
            $userRepoStateChange = $em->getRepository(User::class);
            $userRepoStateChange->setState($user,14);
            return $this->render('@Softphone/hardphone.html.twig', [
                'queues' => $this->toJson($queueList),
                'ivrList' => $this->toJson($ivrList2),
                'breakTypesList' => $this->toJson($breakTypesList),
                'acwTypesList' => $this->toJson($acwypesList),
                'reloadState'=>$reloadState,
                'reloadStateText'=> $reloadStateText,
            ]);
        }else{
            return new Response("Bu kullanıcı başka bir bilgisayarda açık..!!");
        }
    }

    /**
     * @Route("/api/hardphone/sipservercall", name="api_hardphone_sipservercall")
     * @return Response
     */
    public function sipservercall(SipServerService $sipServerService)
    {
        return $this->json(["sipServerCall" => $sipServerService->serverName()->getContent()]);
    }





    /**
     * @Route("/api/hardphone/acwLogStart/{acwType}", name="api_hardphone_acw_log_start")
     * @param UserInterface $user
     * @param Request $request
     * @param AcwType $acwType
     * @return JsonResponse
     * @throws \Exception
     */
    public function acwLogStart(UserInterface $user, AcwType $acwType)
    {

        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository(User::class);

        $userRepo->closeOpenBreaks($user);

        $apply = $userRepo->applyOrder($user);
        if (is_array($apply)) {
            $state = $apply['state'];
            $text = $apply['name'];
        } else {

            $acwLog = new AcwLog();
            $acwLog->setUser($user)
                ->setDuration(0)
                ->setStartTime(new \DateTime())
                ->setAcwType($acwType);

            $em->persist($acwLog);
            $em->flush();

            $text = $acwType->getName();
            $state = $acwType->getState();
        }

        $userRepo->setState($user, $state);

        return new JsonResponse(["state" => $state, "text" => $text]);
    }


    /**
     * @Route("/api/hardphone/get-sip-server-change", name="api_hardphone_get_sip_server_change")
     * @param SipServerService $sipServerService
     * @return Response
     */
    public function getSipServerChange(SipServerService $sipServerService)
    {
        return new Response($sipServerService->serverName()->getContent());
    }


    /**
     * @Route("/api/hardphone/set-state-event/{stateId}", name="api_hardphone_set_state_event")
     * @param UserInterface $user
     * @param $stateId
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function setStateEvent(UserInterface $user, $stateId)
    {

        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository(User::class);

        if ($stateId == 8 or $stateId == 12){
            $userRepo->closeOpenBreaks($user);
        }

        $userRepo->setState($user, $stateId);

        return new Response("Ok");

    }

    /**
     * @Route("/api/hardphone/state", name="api_hardphone_state")
     * @param UserInterface $user
     * @return Response
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function state(UserInterface $user)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(RegisterLog::class)->findBy(["user" => $user, "EndTime" => null]);


        if (count($repo) == 1) {
            $repo[0]->setLastRegister(new \DateTime());
            $em->persist($repo[0]);
            $em->flush();
        } else {
            $registerLog = new RegisterLog();
            $registerLog
                ->setUser($user)
                ->setStartTime(new \DateTime())
                ->setLastRegister(new \DateTime());
            $em->persist($registerLog);
            $em->flush();
        }

        $returnData['success'] = "ok2";

        $userRepo = $em->getRepository(User::class);

        $hasCall = $em->getRepository(Calls::class)->findOneBy(['user' => $user , 'callStatus' => 'active', 'callType'=> ["Inbound","Outbound"]]);

        $onRegister = $em->getRepository(PsContacts::class)->findOneBy(["endpoint"=>$user->getUserProfile()->getExtension()]);

        if (is_null($hasCall)) {

            $holdLogs = $em->getRepository(HoldLog::class)->findBy(["user"=>$user,"endTime"=>null]);
            if (count($holdLogs) > 0) {
                foreach ($holdLogs as $holdLog) {
                    $duration = Date::diffDateTimeToSecond(new \DateTime(),$holdLog->getStartTime());
                    $holdLog
                        ->setEndTime(new \DateTime())
                        ->setDuration($duration);
                    $em->persist($holdLog);
                    $em->flush();
                }
            }

            $apply = $userRepo->applyOrder($user);

            if (is_array($apply)) {
                $returnData['state'] = $apply['state'];
                $returnData['text']  = $apply['name'];
                $returnData['timeStamp']  = 0;

                $userRepo->setState($user, $apply['state']);
            }else{
                $state = 0;
                $text = "Giriş Yapmamış";
                $timeStamp = 0;

                if ($user->getState() == 0 and !is_null($onRegister)){
                    $state = 1;
                    $text = "Hazır";
                    $timeStamp = 0;
                    $userRepo->setState($user, 1);
                }elseif (in_array($user->getState(),[2,5,6,11])){
                    $acwLog = $em->getRepository(AcwLog::class)->findOneBy(["user"=>$user, "endTime"=>null, "duration"=>0]);
                    $state = $user->getState();
                    $text = $acwLog->getAcwType()->getName();
                    $timeStamp = $acwLog->getStartTime()->getTimestamp();
                }elseif ($user->getState() == 4){
                    $agentBreak = $em->getRepository(AgentBreak::class)->findOneBy(["user"=>$user, "endTime"=>null, "duration"=>0]);
                    $state = $user->getState();
                    $text = $agentBreak->getBreakType()->getName();
                    $timeStamp = $agentBreak->getStartTime()->getTimestamp();
                }elseif ($user->getState() == 1){
                    $state = 1;
                    $text = "Hazır";
                    $timeStamp = $user->getLastStateChange()->getTimestamp();
                } else {
                    $state =  $state = $user->getState();
                    $text = "1444";
                    $timeStamp = $user->getLastStateChange()->getTimestamp();
                }

                $returnData['state'] = $state;
                $returnData['text']  = $text;
                $returnData['timeStamp']  = $timeStamp;
            }
        } else {
            if($hasCall->getCallType() != 'Outbound')
               $userRepo->closeOpenBreaks($user);

            if ($user->getState() != 8){
                $userRepo->setState($user,8);
            }

            $returnData['state'] = 8;
            $returnData['text']  = "Çağrıda";
            $returnData['timeStamp']  = $user->getLastStatechange()->getTimeStamp();
        }

        return $this->json($returnData);
    }


    /**
     * @Route("/api/hardphone/on-register-sip-event", name="api_hardphone_on_register_sip_event")
     * @param UserInterface $user
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function onRegisterSipEvent(UserInterface $user)
    {
        $state = 1;
        $text = "Hazır";
        $timeStamp = 0;

        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository(User::class);
        $agentBreak = $em->getRepository(AgentBreak::class)->findOneBy(["user"=>$user, "endTime" => null, "duration"=> 0]);
        if (!is_null($agentBreak)){
            $state = 4;
            $text = $agentBreak->getBreakType()->getName();
            $timeStamp = $agentBreak->getStartTime()->getTimestamp();
        }
        $acwLog = $em->getRepository(AcwLog::class)->findOneBy(["user"=>$user, "endTime" => null, "duration"=> 0]);
        if (!is_null($acwLog)){
            $state = $acwLog->getAcwType()->getState();
            $text = $acwLog->getAcwType()->getName();
            $timeStamp = $acwLog->getStartTime()->getTimestamp();
        }

        $userRepo->setState($user,$state);

        $returnData['state'] = $state;
        $returnData['text']  = $text;
        $returnData['timeStamp']  = $timeStamp;

        return new JsonResponse($returnData);
    }

    /**
     * @Route("/api/hardphone/acwLogStop", name="api_hardphone_acw_log_stop")
     * @param UserInterface $user
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function acwLogStop(UserInterface $user)
    {

        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository(User::class);

        $userRepo->closeOpenBreaks($user);

        $apply = $userRepo->applyOrder($user);
        if (is_array($apply)) {
            $state = $apply['state'];
            $text = $apply['name'];
        } else {

            $state = 1;
            $text = "Hazır";
            $type = null;
        }
        $userRepo->setState($user, $state);

        return new JsonResponse(["state" => $state, "text" => $text]);
    }


    /**
     * @Route("/api/hardphone/breakStart/{breakType}", name="api_hardphone_breakStart")
     * @param UserInterface $user
     * @param BreakType $breakType
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */

    public function breakStart(UserInterface $user, BreakType $breakType)
    {

        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository(User::class);

        $userRepo->closeOpenBreaks($user);

        $apply = $userRepo->applyOrder($user);
        if (is_array($apply)) {
            $state = $apply['state'];
            $text = $apply['name'];
        } else {

            $agentBreak = new AgentBreak();
            $agentBreak->setUser($user)
                ->setDuration(0)
                ->setStartTime(new \DateTime())
                ->setBreakType($breakType);

            $em->persist($agentBreak);
            $em->flush();
            $type = "AgentBreak";
            $text = $breakType->getName();
            $state = 4;

        }
        $userRepo->setState($user, $state);
        return new JsonResponse(["state" => $state, "text" => $text]);
    }

    /**
     * @Route("/api/hardphone/breakStop", name="api_hardphone_break_stop")
     * @param UserInterface $user
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */

    public function breakStop(UserInterface $user)
    {

        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository(User::class);

        $userRepo->closeOpenBreaks($user);

        $apply = $userRepo->applyOrder($user);
        if (is_array($apply)) {
            $state = 4;
            $text = $apply['name'];
        } else {
            $state = 1;
            $text = "Hazır";
            $type = null;
        }
        $userRepo->setState($user, $state);

        return new JsonResponse(["state" => $state, "text" => $text]);
    }

    /**
     * @Route("/api/hardphone/get-channel-id/{callId}", name="api_hardphone_get_channel_id")
     * @param $callId
     * @return JsonResponse
     * @throws \Exception
     */
    public function getChannelId($callId)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Calls::class);

        $channelID = $repo->findOneBy(["callId" => $callId]);

        return new JsonResponse(["channelId" => $channelID->getchannelId()]);
    }

    /**
     * @Route("/api/hardphone/getLastCalls", name="api_hardphone_get_last_calls")
     * @param UserInterface $user
     * @return JsonResponse
     * @throws \Exception
     */
    public function getLastCalls(UserInterface $user)
    {

        $newdate = new \DateTime();
        $sDate = $newdate->format("Y-m-d 00:00:00");
        $eDate = $newdate->format("Y-m-d 23:59:59");

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Calls::class);
        $outboundCalls = $repo->createQueryBuilder("cl")
            ->select("cl.dtExten, cl.clid")
            ->where("cl.exten =:exten")
            ->andWhere("cl.dtExten between :sDate and :eDate")
            ->andWhere("cl.callType=:ctype")
            ->setParameters(["exten" => $user->getUserProfile()->getExtension(), "sDate" => $sDate, "eDate" => $eDate, "ctype" => "Outbound"])
            ->orderBy("cl.dtExten", "DESC")
            ->setMaxResults(10)
            ->getQuery()
            ->getArrayResult();
        $outboundCallsList = [];
        foreach ($outboundCalls as $oCall) {
            $outboundCallsList[] = [
                'time' => $oCall["dtExten"]->format('H : i : s'),
                'number' => $oCall["clid"]
            ];
        }

        $inboundCalls = $repo->createQueryBuilder("cl")
            ->select("cl.dtExten, cl.clid")
            ->where("cl.exten =:exten")
            ->andWhere("cl.dtExten between :sDate and :eDate")
            ->andWhere("cl.callType=:ctype")
            ->setParameters(["exten" => $user->getUserProfile()->getExtension(), "sDate" => $sDate, "eDate" => $eDate, "ctype" => "Inbound"])
            ->orderBy("cl.dtExten", "DESC")
            ->setMaxResults(10)
            ->getQuery()
            ->getArrayResult();

        $inboundCallsList = [];
        foreach ($inboundCalls as $iCall) {
            $inboundCallsList[] = [
                'time' => $iCall["dtExten"]->format('H : i : s'),
                'number' => $iCall["clid"]
            ];


        }

        return new JsonResponse(["outboundCallsList" => $outboundCallsList, "inboundCallsList" => $inboundCallsList]);
    }

    /**
     * @Route("/api/hardphone/igdas-frame-control", name="api_hardphone_igdas_frame_control")
     * @param UserInterface $user
     * @return Response
     */
    public function igdasIframeControl(UserInterface $user)
    {
        $queueIgdas = $this->getDoctrine()->getRepository(UserSkill::class)->findBy(["queue" => 934100106, "member" => $user->getUserProfile()->getExtension()]);
        if (count($queueIgdas) > 0) {
            return new Response(1);
        } else {
            return new Response(0);
        }
    }

    /**
     * @Route("/api/hardphone-vip-modal/{number}", name="hardphone_vip_modal")
     * @param $number
     * @return JsonResponse
     */
    public function vipModal($number)
    {
        $em = $this->getDoctrine()->getManager();

        $guide = $em->getRepository(Guide::class)->findOneBy(["phone"=>$number]);

        $status = [];
        if (is_null($guide)){
            $status["name"] = "";
            $status["title"] = "";
            $status["titleGroup"] = "";
            $status["vipStatus"] = 0;
        }else{
            $status["name"] = $guide->getNameSurname();
            $status["title"] = $guide->getTitle();
            $status["titleGroup"] = $guide->getGuideGroupID()->getName();
            $status["vipStatus"] = 1;
        }
        return $this->json($status);
    }


    /**
     * @Route("/api/hardphone-send-survey/{tbxSipServer}/{channelId}", name="hardphone_send_survey")
     * @param $tbxSipServer
     * @param $channelId
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendSurvey($tbxSipServer, $channelId)
    {

        $client = new \GuzzleHttp\Client();
        $response = $client->request("GET", "https://" . $tbxSipServer . "/api/poll_ob.php?user=amiuser&pass=amiuser&chan=SIP/" . $channelId);
        $content = $response->getBody()->getContents();

        return new JsonResponse($content);
    }


    /**
     * @Route("/api/hardphone-send-inbound-survey/{tbxSipServer}/{channelId}", name="hardphone_send_inbound_survey")
     * @param $tbxSipServer
     * @param $channelId
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendInboundSurvey($tbxSipServer, $channelId)
    {

        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', "https://" . $tbxSipServer . "/api/poll_ib.php?user=amiuser&pass=amiuser&chan=SIP/" . $channelId);
        $content = $response->getBody()->getContents();

        return new JsonResponse($content);
    }
}