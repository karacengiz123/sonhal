<?php
/**
 * Created by PhpStorm.
 * User: cengi
 * Date: 22.02.2019
 * Time: 09:42
 */

namespace App\IbbStaffBundle\Controller;


use App\Asterisk\Entity\QueueLog;
use App\Asterisk\Entity\Queues;
use App\Entity\AcwLog;
use App\Entity\AcwType;
use App\Entity\AgentBreak;
use App\Entity\BreakType;
use App\Entity\Calls;
use App\Entity\Chat;
use App\Entity\Group;
use App\Entity\HoldLog;
use App\Entity\LoginLog;

use App\Entity\RealtimeQueueMembers;
use App\Entity\RegisterLog;
use App\Entity\StateLog;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\UserProfile;

use App\Entity\UserSkill;
use App\Helpers\Date;
use App\Services\AgentStatusService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/chat")
 * Class ccPulseOutboundController
 * @package App\IbbStaffBundle\Controller
 */
class ccPulseChatController extends AbstractController
{
    /**
     * @Route("/ccPulse/queue-select", name="chat_queue_select")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function queueSelectAction()
    {
        $em = $this->getDoctrine()->getManager();
        $queues = $em->getRepository(Queues::class)->findAll();
        $result = [];
        foreach ($queues as $queue) {
            $result [] = ["id" => $queue->getQueue(), "text" => $queue->getDescription()];
        }
        return $this->json($result);
    }

    /**
     * @Route("/ccPulse/team-select", name="chat_team_select_A")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function teamSelectActionA()
    {
        $em = $this->getDoctrine()->getManager();
        $teams = $em->getRepository(Team::class)->findAll();
        $result = [];
        foreach ($teams as $team) {
            $result [] = ["id" => $team->getId(), "text" => $team->getName()];
        }

        return $this->json($result);
    }

    /**
     * @IsGranted("ROLE_TEAM_GET_SELECT_ALL")
     * @Route("/ccPulse/team-chat-get-select-all", name="chat_team_chat_get_select_all")
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function teamChatGetSelectAll(Request $request, AgentStatusService $agentStatusService)
    {
        $internalState = StateLog::$internalState;

        $em = $this->getDoctrine()->getManager();
        $acwTypesRepository = $em->getRepository(AcwType::class);
        $breakTypesRepository = $em->getRepository(BreakType::class);
        $acwLogsRepository = $em->getRepository(AcwLog::class);
        $agentBreakRepository = $em->getRepository(AgentBreak::class);
        $uspRepo = $em->getRepository(UserProfile::class);
        $userRepo = $em->getRepository(User::class);
        $realtimeQueMember = $em->getRepository(RealtimeQueueMembers::class);
        $callsRepository = $em->getRepository(Calls::class);
        $acwTypes = $acwTypesRepository->findAll();
        $breakTypes = $breakTypesRepository->findAll();
        $newdate = new \DateTime();
        $startDate = $newdate->format("Y-m-d 00:00:00");
        $endDate = $newdate->format("Y-m-d 23:59:59");


///burayı sonrasında siliceksin unutma
//        $startDate = $newdate->format("2019-10-10 00:00:00");
//        $endDate = $newdate->format("2019-10-10 23:59:59");
        $teamID = $request->get("tableId") ?? 'selectAll';
        if ($teamID == "selectAll") {
            $users = $uspRepo->createQueryBuilder("up")
                ->where("up.extension IS NOT NULL")
                ->join("up.user", "u")
                ->join("u.groups", "groups")
                ->andWhere("groups.roles LIKE :roles")
                ->setParameter("roles", "%ROLE_CHAT%");

        } else {
            $users = $uspRepo->createQueryBuilder('up');
            $users
                ->where(
                    $users->expr()->in(
                        'up.user',
                        $userRepo
                            ->createQueryBuilder('us')
                            ->where('us.teamId = :teamId')
                            ->getDQL()
                    )
                )
                ->setParameter('teamId', $teamID)
                ->join("up.user", "u")
                ->join("u.groups", "groups")
                ->andWhere("groups.roles LIKE :roles")
                ->setParameter("roles", "%ROLE_CHAT%");

        }
        $users->select('DISTINCT u.id, CONCAT(up.firstName, \' \', up.lastName) AS fullName,u.lastStateChange, u.state,u.chatStatus,u.chatLastActivity');
        $columnsDataTable [] = ["data" => "fullName", "name" => "fullName", "title" => "Temsilci"];
        $columnsDataTable [] = ["data" => "state", "name" => "state", "title" => "Durum"];
        $templateData["state"] = 0;
        $columnsDataTable [] = ["data" => "stateTime", "name" => "stateTime", "title" => "Durum Süresi"];
        $templateData["stateTime"] = 0;


        $chatStatusArr = [
            0=>'Çevrim Dışı',
            1=>'Çevrim İçi',
            2=>'Görüşmede',
            3=>"Uzakta"
        ];
//        $results[$key]['chatStatus']    = $chatStatusArr[$result['chatStatus']];

        $columnsDataTable [] = ["data" => "chatStatus", "name" => "chatStatus", "title" => "CHAT DURUMU"];
        $templateData["chatStatus"] ="()";
        $columnsDataTable [] = ["data" => "chatStatusTime", "name" => "chatStatusTime", "title" => "CHAT DURUMU SÜRESİ"];
        $templateData["chatStatusTime"] ="()";


        $columnsDataTable [] = ["data" => "regTotal", "name" => "regTotal", "title" => "Toplam Register Süresi"];
        $templateData["regTotal"] = "00:00:00";

        $totalBreak = 0;
        foreach ($breakTypes as $breakType) {
            $columnsDataTable [] = ["data" => "bt" . $breakType->getId(), "name" => "bt" . $breakType->getId(), "title" => $breakType->getName()];
            $breakNames[$breakType->getId()] =  $breakType->getName();
            $templateData["bt" . $breakType->getId()] = "00:00:00";
            $breakTYpeQueries[] = "(select sum(ab_{$breakType->getId()}.duration) from " . AgentBreak::class . "  ab_{$breakType->getId()} where ab_{$breakType->getId()}.startTime between '" . $startDate . "' and '" . $endDate . "' and  ab_{$breakType->getId()}.user = u.id and  ab_{$breakType->getId()}.breakType=" . $breakType->getId() . ")as bt" . $breakType->getId();
        }

        $columnsDataTable [] = ["data" => "breakTotal", "name" => "breakTotal", "title" => "Toplam Mola Süresi"];
        $templateData["breakTotal"] = "00:00:00";

        $btQuery = implode(", ", $breakTYpeQueries);

        foreach ($acwTypes as $acwType) {
            $columnsDataTable [] = ["data" => "acw" . $acwType->getId(), "name" => "acw" . $acwType->getId(), "title" => $acwType->getName()];
            $templateData["acw" . $acwType->getId()] = "00:00:00";
            $acwNames[$acwType->getName()] = $acwType->getName();
            $acwTypeQueries[] = "(select sum(acw_{$acwType->getId()}.duration) from " . AcwLog::class . " acw_{$acwType->getId()} where acw_{$acwType->getId()}.user = u.id and  acw_{$acwType->getId()}.acwType=" . $acwType->getId() . " and  acw_{$acwType->getId()}.startTime between '" . $startDate . "' and '" . $endDate . "') as acw" . $acwType->getId();

        }
        $acwQuery = implode(", ", $acwTypeQueries);


        $registerQuery = "(select sum(reg.duration) from " . RegisterLog::class . " reg  where reg.StartTime between '" . $startDate . "' and '" . $endDate . "' and reg.user = u.id) as regTotal";

        $callsTotalDuration = "(select sum(c.durExten) as totalCallDuration from " . Calls::class . " c
                     where c.callType = 'Inbound' and c.callStatus = 'Done'
              and c.dt between '" . $startDate . "' and '" . $endDate . "'
              and c.user = u.id) as CallsTotalDuration";

        $columnsDataTable [] = ["data" => "CallsTotalCount", "name" => "CallsTotalCount", "title" => "Toplam Chat Adedi"];
        $templateData["CallsTotalCount"] = "0";

        $users->addSelect($registerQuery);
        $callsData = $userRepo->createQueryBuilder('u')
            ->select('u.id as user_id')
            ->addSelect('count(c.id) as cnt')
            ->leftJoin('u.chats', 'c')
            ->groupBy('u.id')
            ->where('c.startTime between :start and :end')
            ->andWhere("c.status=:status")
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->setParameter("status",2)
            ->getQuery()->getArrayResult();


        $acwData = $userRepo->createQueryBuilder('u')
            ->select('u.id as user_id')
            ->addSelect('sum(a.duration) as total')
            ->addSelect('at.id as type_id')
            ->join('u.acwLogs', 'a')
            ->join('a.acwType', 'at')
            ->groupBy('a.acwType')
            ->addGroupBy('a.user')
            ->where('a.startTime between :start and :end')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->orderBy('a.id','ASC')
            ->getQuery()->getArrayResult();


        $breakData = $userRepo->createQueryBuilder('u')
            ->select('u.id as user_id')
            ->addSelect('sum(a.duration) as total')
            ->addSelect('at.id as type_id')
            ->join('u.agentBreaks', 'a')
            ->join('a.breakType', 'at')
            ->groupBy('a.breakType')
            ->addGroupBy('a.user')
            ->where('a.startTime between :start and :end')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->orderBy('a.id','ASC')
            ->getQuery()->getArrayResult();
        $userBreakData = [];
        foreach ($breakData as $bData) {
            $userBreakData[$bData['user_id']][$bData['type_id']] = $bData['total'];
        }
        $userAcwData = [];
        foreach ($acwData as $aData) {
            $userAcwData[$aData['user_id']][$aData['type_id']] = $aData['total'];
        }
        $userCallsData = [];
        foreach ($callsData as $cData) {
            $userId = $cData['user_id'];
            unset($cData['user_id']);
            unset($cData['call_type']);
            $userCallsData[$userId] = $cData;
        }
        $results = $users->getQuery()->getArrayResult();



        foreach ($results as $key => $result) {

            $breakTotal = 0;
            $results[$key] = array_merge($templateData, $results[$key]);

            $results[$key]['regTotal'] = gmdate("H:i:s",$results[$key]['regTotal']??0);
            $results[$key]['state']    = $internalState[$result['state']];

            $acwNames=null;
            $breakNames=null;

            if($results[$key]['state'] == 'AcwLog') {
                $lastAcwData = end($acwData);
                $results[$key]['state'] = $acwNames[$lastAcwData['type_id']];
            } elseif($results[$key]['state'] == 'AgentBreak') {
                $lastAcwData =  end($breakData);
                $results[$key]['state'] = $breakNames[$lastAcwData['type_id']];
            }
            $nowDate= new \DateTime();
            $nowDate=$nowDate->getTimeStamp();
            $lastStateAndTime=$nowDate-$results[$key]['lastStateChange']->getTimeStamp();
            $results[$key]['stateTime'] = "(".gmdate("H:i:s",$lastStateAndTime).")";


//            $results[$key]['state'] .= gm($nowDate - $results[$key]['lastStateChange']->getTimeStamp());

            if($results[$key]['chatStatus'] == 0) {
                $results[$key]['chatStatus'] = "Çevrim Dışı";
            }
            elseif($results[$key]['chatStatus'] == 1) {
                $results[$key]['chatStatus'] = "Çevrim İçi";
            }
            elseif($results[$key]['chatStatus'] == 2) {
                $results[$key]['chatStatus'] = "Görüşmede";
            }
            elseif($results[$key]['chatStatus'] == 3) {
                $results[$key]['chatStatus'] = "Uzakta";
            }

            if (!is_null($results[$key]['chatLastActivity'])){
                $lastChatActivStateandTime= $nowDate-$results[$key]['chatLastActivity']->getTimeStamp();
                $results[$key]['chatStatusTime'] = "(".gmdate("H:i:s",$lastChatActivStateandTime).")";
            }
//            $results[$key]['chatStatus'].=strtotime($nowDate-$results[$key]['lastStateChange']);

            /*********************MOLALAR**********************/
            if (isset($userBreakData[$result['id']])) {
                foreach ($userBreakData[$result['id']] as $typeId => $break) {

                    $breakTotal += $break ?? "0";
                    $results[$key]['bt' . $typeId] = gmdate("H:i:s",$break);
//
                }

            }
            $results[$key]['breakTotal'] = gmdate("H:i:s",$breakTotal);
            /*******************ACW LER*******************/
            if (isset($userAcwData[$result['id']])) {
                $acwTotal = 0;
                foreach ($userAcwData[$result['id']] as $typeId => $acw) {
                    $acwTotal += $acw ?? "0";
                    $results[$key]['acw' . $typeId] = gmdate("H:i:s",$acw);
                }
            }
            /*******************CALLS*******************/

            if (isset($userCallsData[$result['id']])) {
                $results[$key]["CallsTotalCount"]= $userCallsData[$result['id']]['cnt']??0;
            }

            unset($results[$key]['id']);
//            unset($results[$key]['user_id']);
        }

        return $this->json(["columns" => $columnsDataTable, "datas" => $results]);

















        exit;

        $em = $this->getDoctrine()->getManager();
        $acwTypesRepository = $em->getRepository(AcwType::class);
        $breakTypesRepository = $em->getRepository(BreakType::class);
        $acwLogsRepository = $em->getRepository(AcwLog::class);
        $agentBreakRepository = $em->getRepository(AgentBreak::class);
        $uspRepo = $em->getRepository(UserProfile::class);
        $userRepo = $em->getRepository(User::class);
        $realtimeQueMember = $em->getRepository(RealtimeQueueMembers::class);
        $callsRepository = $em->getRepository(Calls::class);
        $acwTypes = $acwTypesRepository->findAll();
        $breakTypes = $breakTypesRepository->findAll();
        $newdate = new \DateTime();
        $sDate = $newdate->format("Y-m-d 00:00:00");
        $eDate = $newdate->format("Y-m-d 23:59:59");

        $teamID = $request->get("tableId");
        if ($teamID == "selectAll") {
            $users = $uspRepo->createQueryBuilder("up")
                ->where("up.extension IS NOT NULL")
                ->leftJoin("up.user","user")
                ->andWhere("user.updatedAt BETWEEN :startDate AND :endDate")
                ->setParameter('startDate', $sDate)
                ->setParameter('endDate', $eDate)
                ->leftJoin("user.groups","groups")
                ->andWhere("groups.roles LIKE :roles")
                ->setParameter("roles","%ROLE_CHAT%")
                ->getQuery()->getResult();
        } else {
            $users = $uspRepo->createQueryBuilder('up');
            $users
                ->where(
                    $users->expr()->in(
                        'up.user',
                        $userRepo
                            ->createQueryBuilder('u')
                            ->where('u.teamId = :teamId')
                            ->getDQL()
                    )
                )
                ->setParameter('teamId', $teamID)
                ->leftJoin("up.user","user")
                ->andWhere("user.updatedAt BETWEEN :startDate AND :endDate")
                ->setParameter('startDate', $sDate)
                ->setParameter('endDate', $eDate)
                ->leftJoin("user.groups","groups")
                ->andWhere("groups.roles LIKE :roles")
                ->setParameter("roles","%ROLE_CHAT%");
            $users = $users->getQuery()->getResult();
        }
        $acws = $acwTypesRepository->createQueryBuilder("at")->getQuery()->getArrayResult();
        $breaks = $breakTypesRepository->createQueryBuilder("at")->getQuery()->getArrayResult();
        $paramArr = [];
        $columnsArr = [];
        $columnsDataTable = [];
        $columnsDataTableControl = true;
        $columns = [];
        foreach ($users as $user) {
            /**
             * @var User $userId
             * @var UserProfile $user
             */
            $userId = $user->getUser();
//            $roles = $userId->getRoles();
//            if (array_search("ROLE_CHAT",$roles) > -1) {
            $columnsArr ["userName"] = $user->getFirstName() . " " . $user->getLastName();
            if ($columnsDataTableControl == true) {
                $columnsDataTable [] = ["data" => "userName", "name" => "userName", "title" => "TEMSİLCİ"];
            }

            $columnsArr ["status"] = $agentStatusService->status($userId)->getContent();
            if ($columnsDataTableControl == true) {
                $columnsDataTable [] = ["data" => "status", "name" => "status", "title" => "DURUMU"];
            }

            $chatStatusArr = ['Çevrim Dışı', 'Çevrim İçi', 'Görüşmede', "Uzakta"];
            if (!is_null($userId->getChatLastActivity()) && !is_null($userId->getChatStatus())){
                $chatStatus = $chatStatusArr[$userId->getChatStatus()]." (".gmdate("H:i:s",$newdate->getTimestamp() - $userId->getChatLastActivity()->getTimestamp()).")";
            }else{
                $chatStatus =  "()";
            }

            $columnsArr ["chatStatus"] = $chatStatus;
            if ($columnsDataTableControl == true) {
                $columnsDataTable [] = ["data" => "chatStatus", "name" => "chatStatus", "title" => "CHAT DURUMU"];
            }

            $loginLogs = $em->getRepository(LoginLog::class)->createQueryBuilder("ll")
                ->where("ll.userId=:user")
                ->andWhere("ll.StartTime BETWEEN :startDate AND :endDate")
                ->setParameters([
                    "user" => $userId,
                    "startDate" => $sDate,
                    "endDate" => $eDate,
                ])->getQuery()->getResult();

            $loginTime = 0;
            /**
             * @var LoginLog $loginLog
             */
            foreach ($loginLogs as $loginLog) {
                if (is_null($loginLog->getEndTime())) {
                    $loginTime += $newdate->getTimestamp() - $loginLog->getStartTime()->getTimestamp();
                } else {
                    $loginTime += $loginLog->getEndTime()->getTimestamp() - $loginLog->getStartTime()->getTimestamp();
                }
            }

            $columnsArr ["loginTime"] = gmdate("H:i:s", $loginTime);
            if ($columnsDataTableControl == true) {
                $columnsDataTable [] = ["data" => "loginTime", "name" => "loginTime", "title" => "TOPLAM LOGİN SÜRESİ"];
            }

            //////durum bilgisi dışındaki bilgiler
            ///
            /// AGENT toplam  MOLA BİLGİSİ
            $agentBreakTimes = [];
            $totalBreak = 0;
            foreach ($breakTypes as $breakType) {
                $breakSum = $agentBreakRepository->createQueryBuilder("ab")
                    ->select("SUM(ab.duration)")
                    ->where("ab.user=:user")
                    ->andWhere("ab.breakType=:breakType")
                    ->andWhere("ab.startTime BETWEEN :startDate AND :endDate")
                    ->setParameters([
                        "user" => $userId,
                        "startDate" => $sDate,
                        "endDate" => $eDate,
                        "breakType" => $breakType
                    ])->getQuery()->getSingleScalarResult();

                if ($breakSum == null) {
                    $breakSum = 0;
                } else {
                    $breakSum = $breakSum + 0;
                }

                $columnsArr [$breakType->getName()] = gmdate("H:i:s", $breakSum);
                if ($columnsDataTableControl == true) {
                    $columnsDataTable [] = ["data" => $breakType->getName(), "name" => $breakType->getName(), "title" => $breakType->getName()];
                }

                $totalBreak += $breakSum;
            }

            $columnsArr ["totalBreak"] = gmdate("H:i:s", $totalBreak);
            if ($columnsDataTableControl == true) {
                $columnsDataTable [] = ["data" => "totalBreak", "name" => "totalBreak", "title" => "TOPLAM MOLA SÜRESİ"];
            }

            //////durum bilgisi dışındaki bilgiler
            ///
            /// AGENT toplam  ACW BİLGİSİ
            foreach ($acwTypes as $acwType) {
                $acwSum = $acwLogsRepository->createQueryBuilder("al")
                    ->select("SUM(al.duration)")
                    ->where("al.user=:user")
                    ->andWhere("al.acwType=:acwType")
                    ->andWhere("al.startTime BETWEEN :startDate AND :endDate")
                    ->setParameters([
                        "user" => $userId,
                        "startDate" => $sDate,
                        "endDate" => $eDate,
                        "acwType" => $acwType
                    ])->getQuery()->getSingleScalarResult();

                if ($acwSum == null) {
                    $acwSum = 0;
                } else {
                    $acwSum = $acwSum + 0;
                }

                $columnsArr [$acwType->getName()] = gmdate("H:i:s", $acwSum);
                if ($columnsDataTableControl == true) {
                    $columnsDataTable [] = ["data" => $acwType->getName(), "name" => $acwType->getName(), "title" => $acwType->getName()];
                }
            }

            $totalChat = $this->getDoctrine()->getRepository(Chat::class)->createQueryBuilder("c")
                ->where("c.user=:user")
                ->setParameter("user",$userId)
                ->andWhere("c.status=:status")
                ->setParameter("status",2)
                ->andWhere("c.startTime BETWEEN :startDate AND :endDate")
                ->setParameter("startDate",$sDate)
                ->setParameter("endDate",$eDate)
                ->getQuery()->getResult();

            $columnsArr ["totalChat"] = count($totalChat);
            if ($columnsDataTableControl == true) {
                $columnsDataTable [] = ["data" => "totalChat", "name" => "totalChat", "title" => "Toplam Chat Adedi"];
            }

            $paramArr[] = $columnsArr;
            if ($columnsDataTableControl == true) {
                $columns = $columnsDataTable;
                $columnsDataTableControl = false;
            }
        }
//        }

        return $this->json(["columns" => $columns, "datas" => $paramArr]);
    }
}