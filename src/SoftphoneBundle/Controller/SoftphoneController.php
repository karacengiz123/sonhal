<?php

namespace App\SoftphoneBundle\Controller;

use App\Asterisk\Entity\Config;
use App\Asterisk\Entity\Extens;
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
use App\Repository\AcwLogRepository;
use App\Repository\AgentBreakRepository;
use App\Repository\CallsRepository;
use App\Repository\HoldLogRepository;
use App\Repository\PsContactsRepository;
use App\Repository\RegisterLogRepository;
use App\Repository\UserRepository;
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

class SoftphoneController extends AbstractController
{

    public function toJson($data)
    {
        return $this->container->get('serializer')->serialize($data, 'json');
    }

    /**
     * @Route("/softphone", name="index_softphone2")
     * @param UserInterface $user
     * @param SipServerService $sipServerService
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function indexSoftphone2Action(UserInterface $user , SipServerService $sipServerService)
    {


        $em = $this->getDoctrine()->getManager();


        $queueList = [];
        $queues = $this->getDoctrine()->getManager('asterisk')->getRepository(Queues::class)->findAll();
        foreach ($queues as $queue) {
            $queueList[$queue->getQueue()] = $queue->getDescription();
        }



        $ivrList2 = [];
        $ivrLists = $this->getDoctrine()->getManager('asterisk')->getRepository(Ivr::class)
            ->createQueryBuilder('i')->orderBy("i.description", "ASC")->getQuery()->getResult();

//        $ivrList= array_column($ivrList , 'title' , 'idx');

        foreach ($ivrLists as $ivrList) {
            if ($ivrList->getTitle() != "") {
                $ivrList2 [0] = "Ivr Listesi Seçiniz";
                $ivrList2 [$ivrList->getIdx()] = $ivrList->getTitle() . " - " . $ivrList->getDescription();
            }
        }


        $breakTypesList = $this->getDoctrine()->getManager()->getRepository(BreakType::class)->findAll();
        $acwypesList = $this->getDoctrine()->getManager()->getRepository(AcwType::class)->findByRole('ROLE_AGENT');

        /**
         * @var User $user
         */


        $reloadStateResult = $this->state($user);
        $reloadStateResult = json_decode($reloadStateResult->getContent());
        $reloadState = $reloadStateResult->state;
        $reloadStateText = $reloadStateResult->text;

       // $agentOnlineControl = $this->getDoctrine()->getRepository(PsContacts::class)->findOneBy(["endpoint" => $user->getUserProfile()->getExtension()]);
       // if (is_null($agentOnlineControl)) {
            $userRepoStateChange = $em->getRepository(User::class);
            $userRepoStateChange->setState($user, 14);


            return $this->render('@Softphone/softphone.html.twig', [
                'queues' => $this->toJson($queueList),
                'tbxSipServer' => $sipServerService->serverName()->getContent(),
                'ivrList' => $this->toJson($ivrList2),
                'breakTypesList' => $this->toJson($breakTypesList),
                'acwTypesList' => $this->toJson($acwypesList),
                'reloadState' => $reloadState,
                'reloadStateText' => $reloadStateText,
            ]);
     //   } else {
     //       return new Response("Bu kullanıcı başka bir bilgisayarda açık olabilir..! Lütfen Sayfayı (CTRL + F5) Tuşuna Basarak Bir Daha Yenileyiniz..!!");
     //   }
    }

    /**
     * @Route("/hardphone", name="index_hardphone")
     * @param UserInterface $user
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function indexHardPhone(UserInterface $user)
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

//        $ivrList= array_column($ivrList , 'title' , 'idx');

        foreach ($ivrLists as $ivrList){
            if ($ivrList->getTitle() != ""){
                $ivrList2 [0] = "Ivr Listesi Seçiniz";
                $ivrList2 [$ivrList->getIdx()] = $ivrList->getTitle() ." - ". $ivrList->getDescription();
            }
        }

        $breakTypesList = $this->getDoctrine()->getManager()->getRepository(BreakType::class)->findAll();
        $acwypesList = $this->getDoctrine()->getManager()->getRepository(AcwType::class)->findBy(["role"=>"ROLE_AGENT","state"=>5]);

        /**
         * @var User $user
         */
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
     * @Route("/api/softphone/get-sip-server-change", name="api_softphone_get_sip_server_change")
     * @param SipServerService $sipServerService
     * @return Response
     */
    public function getSipServerChange(SipServerService $sipServerService)
    {
        return new Response($sipServerService->serverName()->getContent());
    }

    /**
     * @Route("/api/softphone/set-state-event/{stateId}", name="api_softphone_set_state_event")
     * @param UserInterface $user
     * @param $stateId
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function setStateEvent(UserInterface $user, $stateId)
    {
        $em = $this->getDoctrine()->getManager();
        $userRepository = $em->getRepository(User::class);

        if ($stateId == 8 or $stateId == 12) {
            /**
             * @var User $user
             */
            $userRepository->closeOpenBreaks($user);
        }

        $userRepository->setState($user, $stateId);

        return new Response("Ok");
    }

    /**
     * @Route("/api/softphone/state", name="api_softphone_state")
     * @param UserInterface $user
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function state(UserInterface $user)
    {
        $em = $this->getDoctrine()->getManager();
        $userRepository = $em->getRepository(User::class);
        $registerLogRepository = $em->getRepository(RegisterLog::class);
        $callsRepository = $em->getRepository(Calls::class);
        $psContactsRepository = $em->getRepository(PsContacts::class);
        $holdLogRepository = $em->getRepository(HoldLog::class);
        $acwLogRepository = $em->getRepository(AcwLog::class);
        $agentBreakRepository = $em->getRepository(AgentBreak::class);

        /**
         * @var User $user
         */
        $registerLogQuery = $registerLogRepository->findOneBy(["user" => $user, "EndTime" => null]);

        if (!is_null($registerLogQuery)) {
            $nowTime = new \DateTime();
            $duration = $nowTime->getTimestamp() - $registerLogQuery->getStartTime()->getTimeStamp();
            $registerLogQuery
                ->setDuration($duration)
                ->setLastRegister($nowTime);

            $em->persist($registerLogQuery);
            $em->flush();
        } else {
            $registerLog = new RegisterLog();
            $registerLog
                ->setUser($user)
                ->setStartTime(new \DateTime())
                ->setLastRegister(new \DateTime())
                ->setDuration(0);
            $em->persist($registerLog);
            $em->flush();
        }

        $returnData['success'] = "ok2";

        $hasCall = $callsRepository->findOneBy(['user' => $user, 'callStatus' => 'active', 'callType' => ["Inbound", "Outbound"]]);

        $hasRegister = $psContactsRepository->findOneBy(['endpoint' => $user->getUserProfile()->getExtension()]);


        if(is_null($hasRegister)) {

            $returnData['state'] = 14;
            $returnData['text'] = "Bağlantı Yok";
            $returnData['timeStamp'] = 0;
            $userRepository->setState($user, 14);
            return $this->json($returnData);
        }
        $onRegister = $psContactsRepository->findOneBy(["endpoint" => $user->getUserProfile()->getExtension()]);

        if (is_null($hasCall)) {
            $holdLogs = $holdLogRepository->findBy(["user" => $user, "endTime" => null]);
            if (count($holdLogs) > 0) {
                $newDateTime = new \DateTime();
                foreach ($holdLogs as $holdLog) {
                    $duration = $newDateTime->getTimestamp() - $holdLog->getStartTime()->getTimestamp();
                    $holdLog
                        ->setEndTime($newDateTime)
                        ->setDuration($duration);
                    $em->persist($holdLog);
                    $em->flush();
                }
            }

            $apply = $userRepository->applyOrder($user);

            if (is_array($apply)) {
                $returnData['state'] = $apply['state'];
                $returnData['text'] = $apply['name'];
                $returnData['timeStamp'] = 0;

                $userRepository->setState($user, $apply['state']);
            } else {
                $state = 0;
                $text = "Giriş Yapmamış";
                $timeStamp = 0;

                if ($user->getState() == 0 and !is_null($onRegister)) {
                    $state = 1;
                    $text = "Hazır";
                    $timeStamp = 0;
                    $userRepository->setState($user, 1);
                } elseif (in_array($user->getState(), [2, 5, 6, 11])) {
                    $acwLog = $acwLogRepository->findOneBy(["user" => $user, "endTime" => null, "duration" => 0]);
                    if (!is_null($acwLog)) {
                        $state = $user->getState();
                        $text = $acwLog->getAcwType()->getName();
                        $timeStamp = $acwLog->getStartTime()->getTimestamp();
                    }
                } elseif ($user->getState() == 4) {
                    $agentBreak = $agentBreakRepository->findOneBy(["user" => $user, "endTime" => null, "duration" => 0]);
                    if (!is_null($agentBreak)) {
                        $state = $user->getState();
                        $text = $agentBreak->getBreakType()->getName();
                        $timeStamp = $agentBreak->getStartTime()->getTimestamp();
                    }
                } elseif ($user->getState() == 1) {
                    $state = 1;
                    $text = "Hazır";
                    $timeStamp = $user->getLastStateChange()->getTimestamp();
                } else {
                    $state = $state = $user->getState();
                    $text = "1444";
                    $timeStamp = $user->getLastStateChange()->getTimestamp();
                }

                $returnData['state'] = $state;
                $returnData['text'] = $text;
                $returnData['timeStamp'] = $timeStamp;
            }
        } else {
            if ($hasCall->getCallType() != 'Outbound') {
                $userRepository->closeOpenBreaks($user);
            }

            if ($user->getState() != 8) {
                $userRepository->setState($user, 8);
            }

            $returnData['state'] = 8;
            $returnData['text'] = "Çağrıda";
            $returnData['timeStamp'] = $user->getLastStatechange()->getTimeStamp();
        }


        return $this->json($returnData);
    }

    /**
     * @Route("/api/hardphone/state", name="api_hardphone_state")
     * @param UserInterface $user
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function hardphoneState(UserInterface $user)
    {
        $em = $this->getDoctrine()->getManager();
        $userRepository = $em->getRepository(User::class);
        $registerLogRepository = $em->getRepository(RegisterLog::class);
        $callsRepository = $em->getRepository(Calls::class);
        $psContactsRepository = $em->getRepository(PsContacts::class);
        $holdLogRepository = $em->getRepository(HoldLog::class);
        $acwLogRepository = $em->getRepository(AcwLog::class);
        $agentBreakRepository = $em->getRepository(AgentBreak::class);

        /**
         * @var User $user
         */
        $registerLogQuery = $registerLogRepository->findOneBy(["user" => $user, "EndTime" => null]);

        if (!is_null($registerLogQuery)) {
            $registerLogQuery
                ->setLastRegister(new \DateTime());
            $em->persist($registerLogQuery);
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

        $hasCall = $callsRepository->findOneBy(['user' => $user , 'callStatus' => 'active', 'callType'=> ["Inbound","Outbound"]]);

        $onRegister = $psContactsRepository->findOneBy(["endpoint"=>$user->getUserProfile()->getExtension()]);

        if (is_null($hasCall)) {
            $holdLogs = $holdLogRepository->findBy(["user"=>$user, "endTime"=>null]);
            if (count($holdLogs) > 0) {
                $newDateTime = new \DateTime();
                foreach ($holdLogs as $holdLog) {
                    $duration = $newDateTime->getTimestamp() - $holdLog->getStartTime()->getTimestamp();
                    $holdLog
                        ->setEndTime($newDateTime)
                        ->setDuration($duration);
                    $em->persist($holdLog);
                    $em->flush();
                }
            }

            $apply = $userRepository->applyOrder($user);

            if (is_array($apply)) {
                $returnData['state'] = $apply['state'];
                $returnData['text']  = $apply['name'];
                $returnData['timeStamp']  = 0;

                $userRepository->setState($user, $apply['state']);
            }else{
                $state = 0;
                $text = "Giriş Yapmamış";
                $timeStamp = 0;

                if ($user->getState() == 0){
                    $state = 1;
                    $text = "Hazır";
                    $timeStamp = 0;
                    $userRepository->setState($user, 1);
                }elseif (in_array($user->getState(),[2,5,6,11])){
                    $acwLog = $acwLogRepository->findOneBy(["user"=>$user, "endTime"=>null, "duration"=>0]);
                    if (!is_null($acwLog)){
                        $state = $user->getState();
                        $text = $acwLog->getAcwType()->getName();
                        $timeStamp = $acwLog->getStartTime()->getTimestamp();
                    }
                }elseif ($user->getState() == 4){
                    $agentBreak = $agentBreakRepository->findOneBy(["user"=>$user, "endTime"=>null, "duration"=>0]);
                    if (!is_null($agentBreak)){
                        $state = $user->getState();
                        $text = $agentBreak->getBreakType()->getName();
                        $timeStamp = $agentBreak->getStartTime()->getTimestamp();
                    }
                }elseif ($user->getState() == 1){
                    $state = 1;
                    $text = "Hazır";
                    $timeStamp = $user->getLastStateChange()->getTimestamp();
                } else {
                    $state =  $state = $user->getState();
                    $text = "1444";
                    $timeStamp = $user->getLastStateChange()->getTimestamp();
                }

                if ($state == 8){
                    $state = 1;
                    $text = "Hazır";
                    $timeStamp = 0;
                    $userRepository->setState($user, 1);
                }

                $returnData['state'] = $state;
                $returnData['text']  = $text;
                $returnData['timeStamp']  = $timeStamp;
            }
        } else {
            if($hasCall->getCallType() != 'Outbound'){
                $userRepository->closeOpenBreaks($user);
            }

            if ($user->getState() != 8){
                $userRepository->setState($user,8);
            }

            $returnData['state'] = 8;
            $returnData['text']  = "Çağrıda";
            $returnData['timeStamp']  = $user->getLastStatechange()->getTimeStamp();
        }


        return $this->json($returnData);
    }

    /**
     * @Route("/api/softphone/on-register-sip-event", name="api_softphone_on_register_sip_event")
     * @param UserInterface $user
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function onRegisterSipEvent(UserInterface $user)
    {
        $em = $this->getDoctrine()->getManager();
        $userRepository = $em->getRepository(User::class);
        $agentBreakRepository = $em->getRepository(AgentBreak::class);
        $acwLogRepository = $em->getRepository(AcwLog::class);

        $state = 1;
        $text = "Hazır";
        $timeStamp = 0;

        /**
         * @var User $user
         */
        $agentBreak = $agentBreakRepository->findOneBy(["user" => $user, "endTime" => null, "duration" => 0]);
        if (!is_null($agentBreak)) {
            $state = 4;
            $text = $agentBreak->getBreakType()->getName();
            $timeStamp = $agentBreak->getStartTime()->getTimestamp();
        }
        $acwLog = $acwLogRepository->findOneBy(["user" => $user, "endTime" => null, "duration" => 0]);
        if (!is_null($acwLog)) {
            $state = $acwLog->getAcwType()->getState();
            $text = $acwLog->getAcwType()->getName();
            $timeStamp = $acwLog->getStartTime()->getTimestamp();
        }

        $userRepository->setState($user, $state);

        $returnData['state'] = $state;
        $returnData['text'] = $text;
        $returnData['timeStamp'] = $timeStamp;

        return new JsonResponse($returnData);
    }

    /**
     * @Route("/api/hardphone/on-register-sip-event/{extension}", name="api_hardphone_on_register_sip_event")
     * @param UserInterface $user
     * @param $extension
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function hardphoneOnRegisterSipEvent(UserInterface $user,$extension)
    {
        $em = $this->getDoctrine()->getManager();
        $userRepository = $em->getRepository(User::class);
        $agentBreakRepository = $em->getRepository(AgentBreak::class);
        $acwLogRepository = $em->getRepository(AcwLog::class);

        $state = 1;
        $text = "Hazır";
        $timeStamp = 0;

        /**
         * Add Realtime Queue Members
         */
        $userSkillRepository = $this->getDoctrine()->getRepository(UserSkill::class);
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
                    ->setStateInterface("SIP/" . $userSkill->getMember())
                    ->setPenalty($userSkill->getPenalty())
                    ->setPaused(1);
                $em->persist($rtqm);
                $em->flush();
            }
        }
        /**
         * @var User $user
         */
        $agentBreak = $agentBreakRepository->findOneBy(["user" => $user, "endTime" => null, "duration" => 0]);
        if (!is_null($agentBreak)) {
            $state = 4;
            $text = $agentBreak->getBreakType()->getName();
            $timeStamp = $agentBreak->getStartTime()->getTimestamp();
        }
        $acwLog = $acwLogRepository->findOneBy(["user" => $user, "endTime" => null, "duration" => 0]);
        if (!is_null($acwLog)) {
            $state = $acwLog->getAcwType()->getState();
            $text = $acwLog->getAcwType()->getName();
            $timeStamp = $acwLog->getStartTime()->getTimestamp();
        }

        $userRepository->setState($user, $state);

        $returnData['state'] = $state;
        $returnData['text'] = $text;
        $returnData['timeStamp'] = $timeStamp;

        $extens = $em->getRepository(Extens::class)->findBy(["user"=>$user]);
        /** @var Extens $exten */
        foreach ($extens as $exten){
            $exten->setUser(null);
            $em->persist($exten);
            $em->flush();
        }
        $extenRes = $em->getRepository(Extens::class)->findOneBy(["exten"=>$extension]);
        /** @var Extens $extenRes */
        $extenRes->setUser($user);
        $em->persist($extenRes);
        $em->flush();

        return new JsonResponse($returnData);
    }

    /**
     * @Route("/api/sipservercall", name="sipservercall")
     * @param SipServerService $sipServerService
     * @return JsonResponse
     */
    public function sipservercall(SipServerService $sipServerService)
    {
        return $this->json(["sipServerCall" => $sipServerService->serverName()->getContent()]);
    }

    /**
     * @Route("/api/softphone/get-channel-id/{callId}", name="api_softphone_get_channel_id")
     * @param $callId
     * @return JsonResponse
     * @throws \Exception
     */
    public function getChannelId($callId)
    {

        $em = $this->getDoctrine()->getManager();
        $callsRepo = $em->getRepository(Calls::class);

        $channelID = $callsRepo->findOneBy(["callId" => $callId]);

        return new JsonResponse(["channelId" => $channelID->getChannelId()]);

    }

    /**
     * @Route("/api/softphone/getLastCalls", name="getLastCalls")
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
        /**
         * @var User $user
         */
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
     * @Route("/api/igdas-frame-control")
     * @param UserInterface $user
     * @return Response
     */
    public function igdasIframeControl(UserInterface $user)
    {
        /**
         * @var User $user
         */
        $queueIgdas = $this->getDoctrine()->getRepository(UserSkill::class)->findOneBy(["queue" => 934100106, "member" => $user->getUserProfile()->getExtension()]);
        if (!is_null($queueIgdas)) {
            return new Response(1);
        } else {
            return new Response(0);
        }
    }

    /**
     * @Route("/api/softphone-vip-modal/{number}", name="softphone_vip_modal")
     * @param $number
     * @return JsonResponse
     */
    public function vipModal($number)
    {
        $em = $this->getDoctrine()->getManager();

        $guide = $em->getRepository(Guide::class)->findOneBy(["phone" => ['9'.$number , $number]]);

        $status = [];
        if (is_null($guide)) {
            $status["name"] = "";
            $status["title"] = "";
            $status["titleGroup"] = "";
            $status["vipStatus"] = 0;
        } else {
            $status["name"] = $guide->getNameSurname();
            $status["title"] = $guide->getTitle();
            $status["titleGroup"] = $guide->getGuideGroupID()->getName();
            $status["vipStatus"] = 1;
        }
        return $this->json($status);
    }

    /**
     * @Route("/api/softphone-send-survey/{tbxSipServer}/{channelId}", name="softphone_send_survey")
     * @param $tbxSipServer
     * @param $channelId
     * @return JsonResponse
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
     * @Route("/api/softphone-send-inbound-survey/{tbxSipServer}/{channelId}", name="softphone_send_inbound_survey")
     * @param $tbxSipServer
     * @param $channelId
     * @return JsonResponse
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
