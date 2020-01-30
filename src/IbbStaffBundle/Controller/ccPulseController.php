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


class ccPulseController extends AbstractController
{
    /**
     * @Route("/ccPulse/queue-select", name="queue_select")
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
     * @IsGranted("ROLE_QUEUE_GET_SELECT_ALL")
     * @Route("/ccPulse/queue-get-select-all", name="queue_get_select_all")
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */

    public function queueGetSelectAll(Request $request, AgentStatusService $agentStatusService)
    {
        $internalState = [
            0 => "GİRİŞ YAPMAMIŞ",
            1 => "HAZIR",
            8 => "ÇAĞRIDA",
            2 => "ACW",
            5 => "SORU",
            6 => "DIŞ ARAMA",
            11 => "AcwLog",
            4 => "AgentBreak",
            12 => "Çalıyor",
            13 => "Bağlantı Yok",
            14 => "Bağlantı Yok",
            15 => "Bağlantı Yok",
            16 => "Yeniden Bağlantı Bekleniyor",
            17 => "Dahili Aramada",
            99 => "Anket Araması",
        ];
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
        $queMemberRepository = $em->getRepository(UserSkill::class);

        $param = array();
        $i = 1;

        $queueName = $request->get("tableId")?? 'selectAll';
        if ($queueName == "selectAll") {
            $users = $uspRepo->createQueryBuilder("up")
                ->where("up.extension IS NOT NULL")
                ->leftJoin("up.user", "u")
                ->leftJoin("u.groups", "groups")
                ->andWhere("groups.roles LIKE :roles")
                ->setParameter("roles", "%ROLE_INBOUND%");
        } else {

            $users = $uspRepo->createQueryBuilder('up');
            $users
                ->where(
                    $users->expr()->in(
                        'up.extension',
                        $queMemberRepository
                            ->createQueryBuilder('qmd')
                            ->select("qmd.member")
                            ->where('qmd.queue = :queue')
                            ->getDQL()
                    )
                )
                ->setParameter('queue', $queueName)
                ->leftJoin("up.user", "u")
                ->leftJoin("u.groups", "groups")
                ->andWhere("groups.roles LIKE :roles")
                ->setParameter("roles", "%ROLE_INBOUND%");
        }

        $users->select('DISTINCT u.id, CONCAT(up.firstName, \' \', up.lastName) AS fullName,u.lastStateChange, u.state');
        $columnsDataTable [] = ["data" => "fullName", "name" => "fullName", "title" => "Temsilci"];
        $columnsDataTable [] = ["data" => "state", "name" => "state", "title" => "Durum"];
        $templateData["state"] = 0;
        $columnsDataTable [] = ["data" => "stateTime", "name" => "stateTime", "title" => "Durum Süresi"];
        $templateData["stateTime"] = 0;
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


        foreach ($acwTypes as $acwType) {
            $columnsDataTable [] = ["data" => "acw" . $acwType->getId(), "name" => "acw" . $acwType->getId(), "title" => $acwType->getName()];
            $templateData["acw" . $acwType->getId()] = "00:00:00";
            $acwNames[$acwType->getId()] = $acwType->getName();
            $acwTypeQueries[] = "(select sum(acw_{$acwType->getId()}.duration) from " . AcwLog::class . " acw_{$acwType->getId()} where acw_{$acwType->getId()}.user = u.id and  acw_{$acwType->getId()}.acwType=" . $acwType->getId() . " and  acw_{$acwType->getId()}.startTime between '" . $startDate . "' and '" . $endDate . "') as acw" . $acwType->getId();

        }


        $registerQuery = "(select sum(reg.duration) from " . RegisterLog::class . " reg  where reg.StartTime between '" . $startDate . "' and '" . $endDate . "' and reg.user = u.id) as regTotal";


        $columnsDataTable [] = ["data" => "CallsTotalDuration", "name" => "CallsTotalDuration", "title" => "Toplam Çağrı Süresi"];
        $templateData["CallsTotalDuration"] = "00:00:00";


        $columnsDataTable [] = ["data" => "CallsTotalCount", "name" => "CallsTotalCount", "title" => "Toplam Çağrı Adedi"];
        $templateData["CallsTotalCount"] = "0";

        $users->addSelect($registerQuery);


        $callsData = $userRepo->createQueryBuilder('u')
            ->select('u.id as user_id')
            ->addSelect("c.callType as call_type")
            ->addSelect('sum(c.durExten) as total')
            ->addSelect('count(c.idx) as cnt')
            ->leftJoin('u.userCall', 'c')
            ->groupBy('u.id')
            ->addGroupBy('c.callType')
            ->where('c.dt between :start and :end')
            ->andWhere()
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
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
            $callType = $cData['call_type'];
            unset($cData['user_id']);
            unset($cData['call_type']);
            $userCallsData[$userId][$callType] = $cData;
        }
        $results = $users->getQuery()->getArrayResult();



        foreach ($results as $key => $result) {
            $breakTotal = 0;
            $results[$key] = array_merge($templateData, $results[$key]);
            $results[$key]['regTotal'] = gmdate("H:i:s",$results[$key]['regTotal']??0);
            $results[$key]['state']    = $internalState[$result['state']];
            if($results[$key]['state'] == 'AcwLog') {
                $lastAcwData = end($acwData);
                $results[$key]['state'] = $acwNames[$lastAcwData['type_id']];
            } elseif($results[$key]['state'] == 'AgentBreak') {
                $lastAcwData =  end($breakData);
                $results[$key]['state'] = $breakNames[$lastAcwData['type_id']];
            }
            $nowTime=new \DateTime();
            $nowTimetoTimeStamp=$nowTime->getTimestamp();

            $lastStateChangeTime=$results[$key]['lastStateChange'];
            $lastStateChangeTimeStamp=$lastStateChangeTime->getTimeStamp();
            $stateTimeExt=$nowTimetoTimeStamp - $lastStateChangeTimeStamp;
            $results[$key]['stateTime'] = gmdate("H:i:s",$stateTimeExt);
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
                $results[$key]["CallsTotalDuration"]= gmdate("H:i:s",$userCallsData[$result['id']]['Inbound']["total"]??0);
                $results[$key]["CallsTotalCount"]=$userCallsData[$result['id']]['Inbound']['cnt']??0;
            }

            unset($results[$key]['id']);
        }

        return $this->json(["columns" => $columnsDataTable, "datas" => $results]);



        exit();
        $acws = $em->getRepository(AcwType::class)->createQueryBuilder("at")->getQuery()->getArrayResult();
        $breaks = $em->getRepository(BreakType::class)->createQueryBuilder("at")->getQuery()->getArrayResult();


        $paramArr = [];
        $columnsArr = [];
        $columnsDataTable = [];
        $columnsDataTableControl = true;
        $columns = [];
        foreach ($users as $user) {
            $userId = $user->getUser();

            $columnsArr ["userName"] = $user->getFirstName() . " " . $user->getLastName();
            if ($columnsDataTableControl == true) {
                $columnsDataTable [] = ["data" => "userName", "name" => "userName", "title" => "TEMSİLCİ"];
            }

            $columnsArr ["status"] = $agentStatusService->status($userId)->getContent();
            if ($columnsDataTableControl == true) {
                $columnsDataTable [] = ["data" => "status", "name" => "status", "title" => "DURUMU"];
            }

            $loginLogs = $em->getRepository(RegisterLog::class)->createQueryBuilder("ll")
                ->where("ll.user=:user")
                ->andWhere("ll.StartTime BETWEEN :startDate AND :endDate")
                ->setParameters([
                    "user" => $userId,
                    "startDate" => $sDate,
                    "endDate" => $eDate,
                ])->getQuery()->getResult();

            $loginTime = 0;
            foreach ($loginLogs as $loginLog) {
                if ($loginLog->getEndTime() == null) {
                    $loginTime += Date::diffDateTimeToSecond($newdate, $loginLog->getStartTime());
                } else {
                    $loginTime += Date::diffDateTimeToSecond($loginLog->getEndTime(), $loginLog->getStartTime());
                }
            }

            $columnsArr ["loginTime"] = gmdate("H:i:s", $loginTime);
            if ($columnsDataTableControl == true) {
                $columnsDataTable [] = ["data" => "loginTime", "name" => "loginTime", "title" => "TOPLAM REGİSTER SÜRESİ"];
            }

            //////durum bilgisi dışındaki bilgiler
            ///
            /// AGENT MOLA BİLGİSİ
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
            /// AGENT ACW BİLGİSİ
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

            $totalCallsPersonals = $callsRepository->createQueryBuilder("cl")
                ->where("cl.user=:user")
                ->andWhere("cl.dt between :sDate and :eDate")
                ->andWhere("cl.callType=:ctype")
                ->setParameters([
                    "user" => $userId,
                    "sDate" => $sDate,
                    "eDate" => $eDate,
                    "ctype" => "Inbound"
                ])
                ->getQuery()->getResult();

            /////AGENT GÖRÜŞME SÜRESİ
            $totaldiffCallTime = 0;
            foreach ($totalCallsPersonals as $callTime) {
                if ($callTime->getDtHangUp() == null) {
                    $diffCallTime = Date::diffDateTimeToSecond(new \DateTime(), $callTime->getDtExten());
                } else {
//                    $diffCallTime = $callTime->getDtHangUp()->getTimeStamp() - $callTime->getDtExten()->getTimeStamp();
                    $diffCallTime = $callTime->getDurExten();
                }
                $totaldiffCallTime += $diffCallTime;
            }
            $totalCallTimeResult = gmdate("H:i:s", $totaldiffCallTime);
            $columnsArr ["totalCallTime"] = $totalCallTimeResult;
            if ($columnsDataTableControl == true) {
                $columnsDataTable [] = ["data" => "totalCallTime", "name" => "totalCallTime", "title" => "Toplam Çağrı Süresi"];
            }

            /////AGENT GÖRÜŞME ADEDİ
            $columnsArr ["totalCall"] = count($totalCallsPersonals);
            if ($columnsDataTableControl == true) {
                $columnsDataTable [] = ["data" => "totalCall", "name" => "totalCall", "title" => "Toplam Çağrı Adedi"];
            }

            $paramArr[] = $columnsArr;
            if ($columnsDataTableControl == true) {
                $columns = $columnsDataTable;
                $columnsDataTableControl = false;
            }
        }

        return $this->json(["columns" => $columns, "datas" => $paramArr]);
    }


    /**
     * @Route("/ccPulse/team-select", name="team_select_A")
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
     * @Route("/ccPulse/team-get-select-all", name="team_get_select_all")
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function teamGetSelectAll(Request $request, AgentStatusService $agentStatusService)
    {
        $internalState = [
            0 => "GİRİŞ YAPMAMIŞ",
            1 => "HAZIR",
            8 => "ÇAĞRIDA",
            2 => "ACW",
            5 => "SORU",
            6 => "DIŞ ARAMA",
            11 => "AcwLog",
            4 => "AgentBreak",
            12 => "Çalıyor",
            13 => "Bağlantı Yok",
            14 => "Bağlantı Yok",
            15 => "Bağlantı Yok",
            16 => "Yeniden Bağlantı Bekleniyor",
            17 => "Dahili Aramada",
            99 => "Anket Araması",
        ];
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
                ->setParameter("roles", "%ROLE_INBOUND%");

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
                ->setParameter("roles", "%ROLE_INBOUND%");

        }
        $users->select('DISTINCT u.id, CONCAT(up.firstName, \' \', up.lastName) AS fullName,u.lastStateChange, u.state');

        $columnsDataTable [] = ["data" => "fullName", "name" => "fullName", "title" => "Temsilci"];
        $columnsDataTable [] = ["data" => "state", "name" => "state", "title" => "Durum"];
        $templateData["state"] = 0;
        $columnsDataTable [] = ["data" => "stateTime", "name" => "stateTime", "title" => "Durum Süresi"];
        $templateData["stateTime"] = 0;
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
            $acwNames[$acwType->getId()] = $acwType->getName();
            $acwTypeQueries[] = "(select sum(acw_{$acwType->getId()}.duration) from " . AcwLog::class . " acw_{$acwType->getId()} where acw_{$acwType->getId()}.user = u.id and  acw_{$acwType->getId()}.acwType=" . $acwType->getId() . " and  acw_{$acwType->getId()}.startTime between '" . $startDate . "' and '" . $endDate . "') as acw" . $acwType->getId();

        }
        $acwQuery = implode(", ", $acwTypeQueries);


        $registerQuery = "(select sum(reg.duration) from " . RegisterLog::class . " reg  where reg.StartTime between '" . $startDate . "' and '" . $endDate . "' and reg.user = u.id) as regTotal";

        $callsTotalDuration = "(select sum(c.durExten) as totalCallDuration from " . Calls::class . " c
                     where c.callType = 'Inbound' and c.callStatus = 'Done'
              and c.dt between '" . $startDate . "' and '" . $endDate . "'
              and c.user = u.id) as CallsTotalDuration";

        $columnsDataTable [] = ["data" => "CallsTotalDuration", "name" => "CallsTotalDuration", "title" => "Toplam Çağrı Süresi"];
        $templateData["CallsTotalDuration"] = "00:00:00";

        $callsTotalCount = "(select count(cc.idx) as totalCall  from " . Calls::class . " cc
                     where cc.callType = 'Inbound' and cc.callStatus = 'Done'
              and cc.dt between '" . $startDate . "' and '" . $endDate . "'
              and cc.user = u.id) as CallsTotalCount";

        $columnsDataTable [] = ["data" => "CallsTotalCount", "name" => "CallsTotalCount", "title" => "Toplam Çağrı Adedi"];
        $templateData["CallsTotalCount"] = "0";

        $callsOutBoundTotalCount = "(select count(co.idx) as totalOutBound  from " . Calls::class . " co
                     where co.callType = 'Outbound' and co.callStatus = 'Done'
              and co.dt between '" . $startDate . "' and '" . $endDate . "'
              and co.user = u.id) as totalOutBoundCall";

        $columnsDataTable [] = ["data" => "totalOutBoundCall", "name" => "totalOutBoundCall", "title" => "Giden Çağrı Sayısı"];
        $templateData["totalOutBoundCall"] = "0";

        $callsLocalTotalCount = "(select count(cl.idx) as totalLocal  from " . Calls::class . " cl
                     where cl.callType = 'Local' and cl.callStatus = 'Done'
              and cl.dt between '" . $startDate . "' and '" . $endDate . "'
              and cl.user = u.id) as totalLocalCall";

        $columnsDataTable [] = ["data" => "totalLocalCall", "name" => "totalLocalCall", "title" => "Dahili Konuşma Adedi"];
        $templateData["totalLocalCall"] = "0";


        $callsAvarageDuration = "(select avg(ca.durExten) as avgCallDuration from " . Calls::class . " ca
                     where ca.callType = 'Inbound' and ca.callStatus = 'Done'
              and ca.dt between '" . $startDate . "' and '" . $endDate . "'
              and ca.user = u.id) as CallsAvarageDuration";

        $columnsDataTable [] = ["data" => "CallsAvarageDuration", "name" => "CallsAvarageDuration", "title" => "Ortalama Konuşma Süresi"];
        $templateData["CallsAvarageDuration"] = "00:00:00";

        $users->addSelect($registerQuery);
//        $users->addSelect($btQuery);
//        $users->addSelect($acwQuery);
//        $users->addSelect($callsTotalDuration);
//        $users->addSelect($callsTotalCount);
//        $users->addSelect($callsOutBoundTotalCount);
//        $users->addSelect($callsLocalTotalCount);
//        $users->addSelect($callsAvarageDuration);


        $callsData = $userRepo->createQueryBuilder('u')
            ->select('u.id as user_id')
            ->addSelect("c.callType as call_type")
            ->addSelect('sum(c.durExten) as total')
            ->addSelect('avg(c.durExten) as avarage')
            ->addSelect('count(c.idx) as cnt')
            ->leftJoin('u.userCall', 'c')
            ->groupBy('u.id')
            ->addGroupBy('c.callType')
            ->where('c.dt between :start and :end')
            ->andWhere()
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
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
            $callType = $cData['call_type'];
            unset($cData['user_id']);
            unset($cData['call_type']);
            $userCallsData[$userId][$callType] = $cData;
        }
        $results = $users->getQuery()->getArrayResult();



        foreach ($results as $key => $result) {
            $breakTotal = 0;
            $results[$key] = array_merge($templateData, $results[$key]);
            $results[$key]['regTotal'] = gmdate("H:i:s",$results[$key]['regTotal']??0);
            $results[$key]['state']    = $internalState[$result['state']];
            if($results[$key]['state'] == 'AcwLog') {
                $lastAcwData = end($acwData);
                $results[$key]['state'] = $acwNames[$lastAcwData['type_id']];
            } elseif($results[$key]['state'] == 'AgentBreak') {
                $lastAcwData =  end($breakData);
                $results[$key]['state'] = $breakNames[$lastAcwData['type_id']];
            }
            $nowTime=new \DateTime();
            $nowTimetoTimeStamp=$nowTime->getTimestamp();

            $lastStateChangeTime=$results[$key]['lastStateChange'];
            $lastStateChangeTimeStamp=$lastStateChangeTime->getTimeStamp();
            $stateTimeExt=$nowTimetoTimeStamp - $lastStateChangeTimeStamp;
            $results[$key]['stateTime'] = gmdate("H:i:s",$stateTimeExt);
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
                $results[$key]["CallsTotalDuration"]= gmdate("H:i:s",$userCallsData[$result['id']]['Inbound']["total"]??0);
                $results[$key]["CallsAvarageDuration"]= gmdate("H:i:s",$userCallsData[$result['id']]['Inbound']['avarage']??0);
                $results[$key]["CallsTotalCount"]=$userCallsData[$result['id']]['Inbound']['cnt']??0;
                $results[$key]["totalLocalCall"]=$userCallsData[$result['id']]['Local']['cnt']??0;
                $results[$key]["totalOutBoundCall"]= $userCallsData[$result['id']]['Outbound']['cnt']??0;
            }

            unset($results[$key]['id']);
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
                ->leftJoin("up.user", "user")
                ->leftJoin("user.groups", "groups")
                ->andWhere("groups.roles LIKE :roles")
                ->setParameter("roles", "%ROLE_INBOUND%")
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
                ->leftJoin("up.user", "user")
                ->leftJoin("user.groups", "groups")
                ->andWhere("groups.roles LIKE :roles")
                ->setParameter("roles", "%ROLE_INBOUND%");
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
            $userId = $user->getUser();

            $columnsArr ["userName"] = $user->getFirstName() . " " . $user->getLastName();
            if ($columnsDataTableControl == true) {
                $columnsDataTable [] = ["data" => "userName", "name" => "userName", "title" => "TEMSİLCİ"];
            }

            $columnsArr ["status"] = $agentStatusService->status($userId)->getContent();
            if ($columnsDataTableControl == true) {
                $columnsDataTable [] = ["data" => "status", "name" => "status", "title" => "DURUMU"];
            }

            $loginLogs = $em->getRepository(RegisterLog::class)->createQueryBuilder("ll")
                ->where("ll.user=:user")
                ->andWhere("ll.StartTime BETWEEN :startDate AND :endDate")
                ->setParameters([
                    "user" => $userId,
                    "startDate" => $sDate,
                    "endDate" => $eDate,
                ])->getQuery()->getResult();

            $loginTime = 0;
            foreach ($loginLogs as $loginLog) {
                if ($loginLog->getEndTime() == null) {
                    $loginTime += Date::diffDateTimeToSecond($newdate, $loginLog->getStartTime());
                } else {
                    $loginTime += Date::diffDateTimeToSecond($loginLog->getEndTime(), $loginLog->getStartTime());
                }
            }

            $columnsArr ["loginTime"] = gmdate("H:i:s", $loginTime);
            if ($columnsDataTableControl == true) {
                $columnsDataTable [] = ["data" => "loginTime", "name" => "loginTime", "title" => "TOPLAM REGİSTER SÜRESİ"];
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


            $totalCallsPersonals = $callsRepository->createQueryBuilder("cl")
                ->where("cl.user=:user")
                ->andWhere("cl.dt between :sDate and :eDate")
                ->andWhere("cl.callType=:ctype")
                ->setParameters([
                    "user" => $userId,
                    "sDate" => $sDate,
                    "eDate" => $eDate,
                    "ctype" => "Inbound"
                ])
                ->getQuery()->getResult();

            /////AGENT GÖRÜŞME SÜRESİ
            $totaldiffCallTime = 0;
            foreach ($totalCallsPersonals as $callTime) {
                if ($callTime->getDtHangUp() == null) {
                    $diffCallTime = Date::diffDateTimeToSecond(new \DateTime(), $callTime->getDtExten());
                } else {
//                    $diffCallTime = $callTime->getDtHangUp()->getTimeStamp() - $callTime->getDtExten()->getTimeStamp();
                    $diffCallTime = $callTime->getDurExten();
                }
                $totaldiffCallTime += $diffCallTime;
            }
            $totalCallTimeResult = gmdate("H:i:s", $totaldiffCallTime);
            $columnsArr ["totalCallTime"] = $totalCallTimeResult;
            if ($columnsDataTableControl == true) {
                $columnsDataTable [] = ["data" => "totalCallTime", "name" => "totalCallTime", "title" => "Toplam Çağrı Süresi"];
            }

            /////AGENT GÖRÜŞME ADEDİ
            $columnsArr ["totalCall"] = count($totalCallsPersonals);
            if ($columnsDataTableControl == true) {
                $columnsDataTable [] = ["data" => "totalCall", "name" => "totalCall", "title" => "Toplam  Çağrı Adedi"];
            }

            //AGENT DIS ARAMA SAYISI
            $publicOutboundCalls = $callsRepository->createQueryBuilder("cl")
//                ->select("COUNT(cl.idx)")
                ->where("cl.user=:user")
                ->andWhere("cl.dt BETWEEN :sDate and :eDate")
                ->andWhere("cl.callType=:ctype")
                ->setParameters([
                    "user" => $userId,
                    "sDate" => $sDate,
                    "eDate" => $eDate,
                    "ctype" => "Outbound"
                ])
                ->getQuery()->getResult();


            /////AGENT "Giden Çağrı Sayısı
            $columnsArr ["publicOutboundCalls"] = count($publicOutboundCalls);
            if ($columnsDataTableControl == true) {
                $columnsDataTable [] = ["data" => "publicOutboundCalls", "name" => "publicOutboundCalls", "title" => "Giden Çağrı Sayısı"];
            }


            /////AGENT DAHILI GÖRÜŞME ADEDİ
            $publicExtenChannelCalls = $callsRepository->createQueryBuilder("cl")
                ->where("cl.user=:user")
                ->andWhere("cl.dt BETWEEN :sDate and :eDate")
                ->andWhere("cl.extenChannelId is not null")
                ->setParameters([
                    "user" => $userId,
                    "sDate" => $sDate,
                    "eDate" => $eDate,
                ])
                ->getQuery()->getResult();


            $columnsArr ["publicExtenChannelCalls"] = count($publicExtenChannelCalls);
            if ($columnsDataTableControl == true) {
                $columnsDataTable [] = ["data" => "publicExtenChannelCalls", "name" => "publicExtenChannelCalls", "title" => "Dahili Konuşma Sayısı"];
            }


            /////AGENT ORTALAMA GÖRÜŞME SÜRESİ
            $publicAVGSpeakCalls = $callsRepository->createQueryBuilder("cl")
                ->select("AVG(cl.durExten)")
                ->where("cl.user=:user")
                ->andWhere("cl.dt BETWEEN :sDate and :eDate")
                ->andWhere("cl.callType=:ctype")
                ->setParameters([
                    "user" => $userId,
                    "sDate" => $sDate,
                    "eDate" => $eDate,
                    "ctype" => "Inbound"
                ])
                ->getQuery()->getSingleScalarResult();


            $columnsArr ["publicAVGSpeakCalls"] = gmdate("H:i:s", $publicAVGSpeakCalls);
            if ($columnsDataTableControl == true) {
                $columnsDataTable [] = ["data" => "publicAVGSpeakCalls", "name" => "publicAVGSpeakCalls", "title" => "Ortalama Konuşma Süresi"];
            }

            $paramArr[] = $columnsArr;
            if ($columnsDataTableControl == true) {
                $columns = $columnsDataTable;
                $columnsDataTableControl = false;
            }
        }

        return $this->json(["columns" => $columns, "datas" => $paramArr]);
    }

    /**
     * @IsGranted("ROLE_AGENT_AS_QUEUE_GET_SELECT_ALL")
     * @Route("/ccPulse/agent-as-queue-get-select-all", name="agent_as_queue_get_select_all")
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function agentAsQueueGetSelectAll(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $newdate = new \DateTime();
        $sDate = $newdate->format("Y-m-d 00:00:00");
        $eDate = $newdate->format("Y-m-d 23:59:59");
        $agentBreakRepository = $em->getRepository(AgentBreak::class);
        $acwTypesRepository = $em->getRepository(AcwType::class);
        $acwLogsRepository = $em->getRepository(AcwLog::class);
        $queueRepository = $em->getRepository(Queues::class);
        $queueMemberDynamicRepository = $em->getRepository(UserSkill::class);
        $userProfileRepository = $em->getRepository(UserProfile::class);
        $realtimeQueMember = $em->getRepository(RealtimeQueueMembers::class);
        $callsRepository = $em->getRepository(Calls::class);

//        $client = new \GuzzleHttp\Client();
//        $queueSummarys = $client->request('GET', 'https://'.$this->getParameter('tbxSipServer').'/api/queue_summary.txt');
//        $queueSummarys = $queueSummarys->getBody()->getContents();
//        $queueSummarys = json_decode($queueSummarys,true);

        $queues = $queueRepository->createQueryBuilder("q")
            ->select("q.queue,q.description")
            ->getQuery()
            ->getArrayResult();

        $result = [];
        foreach ($queues as $queue) {

            ////GİRİŞ YAPMIŞLARIN LİSTESİ

            $logedIn = $em->getRepository(User::class)->createQueryBuilder("u")
                ->select("COUNT(u.id)")
                ->where("u.state =:state")
                ->leftJoin("u.realtimeQueueMembers", "rqm")
                ->andWhere("rqm.queueName =:queueName")
                ->setParameters([
                    "state" => 1,
                    "queueName" => $queue["queue"]
                ])
                ->getQuery()->getSingleScalarResult();

            /////aktif ÇAĞRI SAYISI
            ///
            $activeCalls = $callsRepository->createQueryBuilder("cl")
                ->select("count(cl.idx)")
                ->where("cl.dtExten is not null")
                ->andWhere("cl.callStatus=:status")
                ->andWhere("cl.dtHangup is null")
                ->andWhere("cl.dt between :sDate and :eDate")
                ->andWhere("cl.callType=:ctype")
                ->andWhere("cl.queue = :queue")
                ->setParameters(["status" => "Active", "sDate" => $sDate, "eDate" => $eDate, "ctype" => "Inbound", "queue" => $queue["queue"]])
                ->getQuery()->getSingleScalarResult();


            ////MOLADA TOPLAM  LİSTESİ

            $userProfileRepository = $em->getRepository(User::class);
            $inBreak = $userProfileRepository->createQueryBuilder("u");
            $inBreak
                ->select("COUNT(u.id)")
                ->leftJoin("u.realtimeQueueMembers", "rqm")
                ->where(
                    $inBreak->expr()->in(
                        "u.state", [4, 5, 11]
                    )
                )
                ->andWhere("rqm.queueName=:queueName")
                ->andWhere("rqm.paused=:paused")
                ->setParameters([
                    "queueName" => $queue["queue"],
                    "paused" => 1
                ]);
            $inBreak = $inBreak->getQuery()->getSingleScalarResult();

            ////ACW TOPLAM  LİSTESİ
            ///
            $userProfileRepository = $em->getRepository(User::class);
            $inAcw = $userProfileRepository->createQueryBuilder("u");
            $inAcw
                ->select("COUNT(u.id)")
                ->leftJoin("u.realtimeQueueMembers", "rqm")
                ->where(
                    $inAcw->expr()->in(
                        "u.state", [2, 5, 6, 11]
                    )
                )
                ->andWhere("rqm.queueName=:queueName")
                ->andWhere("rqm.paused=:paused")
                ->setParameters([
                    "queueName" => $queue["queue"],
                    "paused" => 1
                ]);
            $inAcw = $inAcw->getQuery()->getSingleScalarResult();


            ////Avail LİSTESİ
            ///
//            $avail = $logedIn - $activeCalls - $inBreak - $inAcw;
            $userProfileRepository = $em->getRepository(User::class);
            $avail = $userProfileRepository->createQueryBuilder("u");
            $avail
                ->select("COUNT(u.id)")
                ->leftJoin("u.realtimeQueueMembers", "rqm")
                ->where(
                    $avail->expr()->in(
                        "u.state", [1]
                    )
                )
                ->andWhere("rqm.queueName=:queueName")
                ->setParameters([
                    "queueName" => $queue["queue"],
                ]);
            $avail = $avail->getQuery()->getSingleScalarResult();

            $result[] = [
                "Queue" => $queue["description"],
                "LoggedIn" => $logedIn,
                "Avail" => $avail,
                "Callers" => $activeCalls,
                "inBreak" => $inBreak,
                "inAcw" => $inAcw
            ];
        }

        $columns = [
            ["data" => "Queue", "name" => "Queue", "title" => "KUYRUK"],
            ["data" => "LoggedIn", "name" => "LoggedIn", "title" => "GİRİŞ YAPMIŞ"],
            ["data" => "Avail", "name" => "Avail", "title" => "ÇAĞRI BEKLEYEN"],
            ["data" => "Callers", "name" => "Callers", "title" => "ÇAĞRIDA"],
            ["data" => "inBreak", "name" => "inBreak", "title" => "MOLADA"],
            ["data" => "inAcw", "name" => "inAcw", "title" => "ACW 'DE (Tuşlamalar Dahil)"],
        ];

        return $this->json(["columns" => $columns, "datas" => $result]);
    }


    /**
     * @IsGranted("ROLE_CALL_AS_QUEUE_GET_SELECT_ALL")
     * @Route("/ccPulse/call-as-queue-get-select-all", name="call_as_queue_get_select_all")
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function callAsQueueGetSelectAll(Request $request)
    {
        $em = $this->getDoctrine()->getManager();


        $newdate = new \DateTime();
        $sDate = $newdate->format("Y-m-d 00:00:00");
        $eDate = $newdate->format("Y-m-d 23:59:59");
        $newdate->modify('-15 minutes');

        $callsRepository = $em->getRepository(Calls::class);

        $queues = $em->getRepository(Queues::class)->createQueryBuilder("q")
            ->select("q.queue,q.description")->getQuery()->getArrayResult();

        $queueLog = $this->getDoctrine()->getRepository(QueueLog::class);


        $rowVal = array();
        foreach ($queues as $queue) {

            /////GELEN ÇAĞRI SAYISI
            $totalCalls = $callsRepository->createQueryBuilder("cl")
                ->select("count(cl.idx)")
                ->where("cl.dt between :sDate and :eDate")
                ->andWhere("cl.callType=:ctype")
                ->andWhere("cl.queue = :queue")
                ->setParameters(["sDate" => $sDate, "eDate" => $eDate, "ctype" => "Inbound", "queue" => $queue["queue"]])
                ->getQuery()->getSingleScalarResult();

            /////Başarılı ÇAĞRI SAYISI

            $completedCalls = $callsRepository->createQueryBuilder("cl")
                ->select("count(cl.idx)")
                ->where("cl.exten is not null")
                ->andWhere("cl.callStatus=:status")
                ->andWhere("cl.dt between :sDate and :eDate")
                ->andWhere("cl.callType=:ctype")
                ->andWhere("cl.queue = :queue")
                ->setParameters(["status" => "Done", "sDate" => $sDate, "eDate" => $eDate, "ctype" => "Inbound", "queue" => $queue["queue"]])
                ->getQuery()->getSingleScalarResult();

            /////Kaçan ÇAĞRI SAYISI
            ///
            $missedCalls = $callsRepository->createQueryBuilder("cl")
                ->select("count(cl.idx)")
                ->where("cl.dtQueue is not null")
                ->andWhere("cl.exten is null")
                ->andWhere("cl.callStatus=:status")
                ->andWhere("cl.dt between :sDate and :eDate")
                ->andWhere("cl.callType=:ctype")
                ->andWhere("cl.queue = :queue")
                ->setParameters(["status" => "Done", "sDate" => $sDate, "eDate" => $eDate, "ctype" => "Inbound", "queue" => $queue["queue"]])
                ->getQuery()->getSingleScalarResult();

            ////////kacırma oranı
            $abandonedAVG = 0;
            if (!$totalCalls == 0) {
                $abandonedAVG = round((($missedCalls / $totalCalls) * 100), 2) . " %";
            }

            //////KUYRUKTA BEKLEYEN CAĞRI SAYISI
            $inwaitingCalls = $this->getDoctrine()->getRepository(Calls::class)->createQueryBuilder("cl")
                ->where("cl.dtQueue is not null")
                ->andwhere("cl.queue is not null")
                ->andWhere("cl.dtExten is null")
                ->andwhere("cl.dt between :sDate and :eDate")
                ->andWhere("cl.callStatus=:status")
                ->andWhere("cl.callType=:ctype")
                ->andWhere("cl.queue=:queue")
                ->setParameters(["sDate" => $sDate, "eDate" => $eDate, "status" => "Active", "ctype" => "Inbound", "queue" => $queue["queue"]])
                ->getQuery()->getResult();

            //////////cevaplama oranı
            $responseRate = 0;
            if (!$totalCalls == 0) {
                $responseRate = round((($completedCalls / $totalCalls) * 100), 2) . " %";
            }

            //////KUYRUKTA BEKLEYEN MAX SÜRE

            $holdTimeMax = $callsRepository->createQueryBuilder("cl")
                ->select("max(cl.durQueue)")
                ->where("cl.dt between :sDate and :eDate")
                ->andWhere("cl.callType=:ctype")
                ->andWhere("cl.queue = :queue")
                ->setParameters(["sDate" => $sDate, "eDate" => $eDate, "ctype" => "Inbound", "queue" => $queue["queue"]])
                ->getQuery()->getSingleScalarResult();
            $holdTimeMax = gmdate("H:i:s", $holdTimeMax);


            //////24 sn den erken cevaplananlar
            $inboundCallListMax24Sec = $callsRepository->createQueryBuilder("cl")
                ->select("count(cl.idx) as sayi")
                ->where("cl.dtExten between :sDate and :eDate")
                ->andWhere("cl.callType=:ctype")
                ->andWhere("cl.queue = :queue")
                ->andWhere("cl.durQueue > 24")
                ->setParameters(["sDate" => $sDate, "eDate" => $eDate, "ctype" => "Inbound", "queue" => $queue["queue"]])
                ->getQuery()->getSingleScalarResult();


            ////Servis seviyesi
            $serviceLevel = 0;
            if (!$completedCalls == 0) {
                $serviceLevel = intval(($inboundCallListMax24Sec / $completedCalls) * 100);
            }

            $resVal["Queue"] = $queue["description"];
            $resVal["Calls"] = $totalCalls;
            $resVal["Completed"] = $completedCalls;
            $resVal["Abandoned"] = $missedCalls;
            $resVal["inwaitingCalls"] = count($inwaitingCalls);
            $resVal["AbandonedAVG"] = $abandonedAVG;
            $resVal["HoldtimeMax"] = $holdTimeMax;
            $resVal["responseRate"] = $responseRate;
            $resVal["ServiceLevel"] = $serviceLevel;

            $rowVal[] = $resVal;

        }

        $columns = [
            ["data" => "Queue", "name" => "Queue", "title" => "KUYRUK"],
            ["data" => "Calls", "name" => "Calls", "title" => "GELEN"],
            ["data" => "Completed", "name" => "Completed", "title" => "CEVAPLANAN"],
            ["data" => "Abandoned", "name" => "Abandoned", "title" => "KAÇAN"],
            ["data" => "inwaitingCalls", "name" => "inwaitingCalls", "title" => "BEKLEYEN"],
            ["data" => "AbandonedAVG", "name" => "AbandonedAVG", "title" => "% KAÇAN ORT"],
            ["data" => "HoldtimeMax", "name" => "HoldtimeMax", "title" => "TÜM GÜN MAX BEKLEME SÜRESİ"],
            ["data" => "responseRate", "name" => "responseRate", "title" => "% CEVAPLANMA ORANI"],
            ["data" => "ServiceLevel", "name" => "ServiceLevel", "title" => "TÜM GÜN SERVİCE LEVEL"]
        ];


        return $this->json(["columns" => $columns, "datas" => $rowVal]);

    }

    /**
     * @IsGranted("ROLE_CCPULSE_SUMMARY")
     * @Route("/ccPulse/summary",name="ccPulse_summary")
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function Summary()
    {

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
        $queMemberRepository = $em->getRepository(UserSkill::class);
        $logins = $em->getRepository(LoginLog::class);
        $holdLogRepo = $em->getRepository(HoldLog::class);

        $newdate = new \DateTime();
        $sDate = $newdate->format("Y-m-d 00:00:00");
        $eDate = $newdate->format("Y-m-d 23:59:59");


        $lastTimeSecond = $newdate->format("Y-m-d H:i:s");
        $firstTimeSecond = $newdate->modify("-15 minute")->format("Y-m-d H:i:s");
        $rows = [];
        $columns = [];
        $loginCountAndSum= $logins->createQueryBuilder('l')
            ->select('sum(l.duration) as total')
            ->addselect('count(l.id) as cnt')
            ->where('l.EndTime is null')
            ->andWhere("l.StartTime BETWEEN :sDate AND :eDate")
            ->setParameter('sDate', $sDate)
            ->setParameter('eDate', $eDate)
            ->getQuery()->getArrayResult();
        foreach ($loginCountAndSum as $value)
        {
            $loginCount=$value["cnt"];
            $loginSum =$value["total"];
        }
        $rows ["loginCount"] = $loginCount;

        $columns [] = ["data" => "loginCount", "name" => "loginCount", "title" => "TOPLAM LOGİN SAYISI"];

///////////////////////////////////////////////logın süresi total////////////////////////////////

        $summaryTotalLoginTimeResult = gmdate("H:i:s", $loginSum);
        $rows ["summaryTotalLoginTimeResult"] = $summaryTotalLoginTimeResult;
        $columns [] = ["data" => "summaryTotalLoginTimeResult", "name" => "summaryTotalLoginTimeResult", "title" => "TOPLAM LOGİN SÜRESİ"];



        //////////////////////////////////// //TALKING INBOUND//////////////////////////////////////////////
        $talkingInbound = $callsRepository->createQueryBuilder("t")
            ->select("COUNT(t.idx)")
            ->where("t.callType =:callType")
            ->andwhere("t.callStatus =:callStatus")
            ->andWhere("t.dt BETWEEN :sDate AND :eDate")
            ->setParameters([

                "callType" => "Inbound",
                "callStatus" => "Active",
                "sDate" => $sDate,
                "eDate" => $eDate,
            ])
            ->getQuery()->getSingleScalarResult();
        $rows ["talkingInbound"] = $talkingInbound;
        $columns [] = ["data" => "talkingInbound", "name" => "talkingInbound", "title" => "TALKING INBOUND"];


        //TALKING OUTBOUND
        $talkingOutbound = $callsRepository->createQueryBuilder("t")
            ->select("COUNT(t.idx)")
            ->where("t.callStatus =:callStatus")
            ->andWhere("t.callType =:callType")
            ->andWhere("t.dt BETWEEN :sDate AND :eDate")
            ->setParameters([
                "callStatus" => "Active",
                "callType" => "Outbound",
                "sDate" => $sDate,
                "eDate" => $eDate,
            ])
            ->getQuery()->getSingleScalarResult();
        $rows ["talkingOutbound"] = $talkingOutbound;

        $columns [] = ["data" => "talkingOutbound", "name" => "talkingOutbound", "title" => "TALKING OUTBOUND"];


        //TOPLAM MOLA SAYISI
        $totalBreakCount = $userRepo->createQueryBuilder("u")
            ->select("COUNT(u.id)")
            ->andWhere("u.state =:state")
            ->andWhere("u.updatedAt BETWEEN :sDate AND :eDate")
            ->setParameters([
                "state" => 4,
                "sDate" => $sDate,
                "eDate" => $eDate,
            ])
            ->getQuery()->getSingleScalarResult();
        $rows ["totalBreakCount"] = $totalBreakCount;

        $columns [] = ["data" => "totalBreakCount", "name" => "totalBreakCount", "title" => "MOLA"];

        //TOPLAM HAZIR SAYISI
        $totalReadyCount = $userRepo->createQueryBuilder("u")
            ->select("COUNT(u.id)")
            ->andWhere("u.state =:state")
            ->andWhere("u.updatedAt BETWEEN :sDate AND :eDate")
            ->setParameters([
                "state" => 1,
                "sDate" => $sDate,
                "eDate" => $eDate,
            ])
            ->getQuery()->getSingleScalarResult();
        $rows ["totalReadyCount"] = $totalReadyCount;

        $columns [] = ["data" => "totalReadyCount", "name" => "totalReadyCount", "title" => "Hazır Olanlar"];

//        //TOPLAM HAZIR OLMAYANLARIN SAYISI
        $totalNotReadyCount = $userRepo->createQueryBuilder("u")
            ->select("COUNT(u.id)")
            ->andWhere("u.state !=:state")
            ->setParameters([
                "state" => 1,
            ])
            ->getQuery()->getSingleScalarResult();
        $rows ["totalNotReadyCount"] = $totalNotReadyCount;

        $columns [] = ["data" => "totalNotReadyCount", "name" => "totalNotReadyCount", "title" => "Hazır Olmayanlar"];


        /////GELEN ÇAĞRI SAYISI///

        $totalCalls = $callsRepository->createQueryBuilder("cl")
            ->select("count(cl.idx)")
            ->where("cl.dt between :sDate and :eDate")
            ->andWhere("cl.callType=:ctype")
            ->andWhere("cl.callStatus=:status")
            ->setParameters(["sDate" => $sDate, "eDate" => $eDate, "ctype" => "Inbound", "status" => "Done"])
            ->getQuery()->getSingleScalarResult();

        $rows ["call"] = $totalCalls;
        $columns [] = ["data" => "call", "name" => "call", "title" => "GELEN"];

        /////Başarılı ÇAĞRI SAYISI

        $completedCalls = $callsRepository->createQueryBuilder("cl")
            ->select("count(cl.idx)")
            ->where("cl.exten is not null")
            ->andWhere("cl.callStatus=:status")
            ->andWhere("cl.dt between :sDate and :eDate")
            ->andWhere("cl.callType=:ctype")
            ->setParameters(["status" => "Done", "sDate" => $sDate, "eDate" => $eDate, "ctype" => "Inbound"])
            ->getQuery()->getSingleScalarResult();

        $rows ["answered"] = $completedCalls;
        $columns [] = ["data" => "answered", "name" => "answered", "title" => "CEVAPLANAN"];

        ///// OUTBOUND CAGRI SAYISI
        ///
        $outboundCalls = $callsRepository->createQueryBuilder("cl")
            ->select("count(cl.idx)")
            ->where("cl.exten is not null")
            ->andWhere("cl.callStatus=:status")
            ->andWhere("cl.dt between :sDate and :eDate")
            ->andWhere("cl.callType=:ctype")
            ->setParameters(["status" => "Done", "sDate" => $sDate, "eDate" => $eDate, "ctype" => "Outbound"])
            ->getQuery()->getSingleScalarResult();

        $rows ["outboundCalls"] = $outboundCalls;
        $columns [] = ["data" => "talkingOutbound", "name" => "talkingOutbound", "title" => "OUTBOUND"];

        /////Kaçan ÇAĞRI SAYISI
        ///
        $missedCalls = $callsRepository->createQueryBuilder("cl")
            ->select("count(cl.idx)")
            ->where("cl.dtQueue is not null")
            ->andWhere("cl.exten is null")
            ->andWhere("cl.callStatus=:status")
            ->andWhere("cl.dt between :sDate and :eDate")
            ->andWhere("cl.callType=:ctype")
            ->setParameters(["status" => "Done", "sDate" => $sDate, "eDate" => $eDate, "ctype" => "Inbound"])
            ->getQuery()->getSingleScalarResult();

        $rows ["abandon"] = $missedCalls;
        $columns [] = ["data" => "abandon", "name" => "abandon", "title" => "KAÇAN"];

        /////IVR DA TAMAMLANAN ÇAĞRI SAYISI
        ///
        $toIvrEndedCalls = $callsRepository->createQueryBuilder("cl")
            ->select("count(cl.idx)")
            ->where("cl.dt is not null")
            ->andWhere("cl.dtQueue is null")
            ->andWhere("cl.callStatus=:status")
            ->andWhere("cl.dt between :sDate and :eDate")
            ->andWhere("cl.callType=:ctype")
            ->setParameters(["status" => "Done", "sDate" => $sDate, "eDate" => $eDate, "ctype" => "Inbound"])
            ->getQuery()->getSingleScalarResult();

        $rows ["toIvrEnded"] = $toIvrEndedCalls;
        $columns [] = ["data" => "toIvrEnded", "name" => "toIvrEnded", "title" => "IVRDA TAMAMLANAN"];

        /////aktif ÇAĞRI SAYISI
        ///
        $activeCalls = $callsRepository->createQueryBuilder("cl")
            ->select("count(cl.idx)")
            ->where("cl.dtExten is not null")
            ->andWhere("cl.callStatus=:status")
            ->andWhere("cl.dtHangup is null")
            ->andWhere("cl.dt between :sDate and :eDate")
            ->andWhere("cl.callType=:ctype")
            ->setParameters(["status" => "Active", "sDate" => $sDate, "eDate" => $eDate, "ctype" => "Inbound"])
            ->getQuery()->getSingleScalarResult();
        $rows ["inwaitingCalls"] = $activeCalls;

        $columns [] = ["data" => "inwaitingCalls", "name" => "inwaitingCalls", "title" => "ŞUAN ÇAĞRIDAKİLER"];

        /////20sn cevaplanan Başarılı ÇAĞRI SAYISI
        ///
        $in20secondAnswered = $callsRepository->createQueryBuilder("cl")
            ->select("count(cl.idx)")
            ->where("cl.exten is not null")
            ->andWhere("cl.callStatus=:status")
            ->andWhere("cl.dt between :sDate and :eDate")
            ->andWhere("cl.callType=:ctype")
            ->andWhere("cl.durQueue <= 20")
            ->setParameters(["status" => "Done", "sDate" => $sDate, "eDate" => $eDate, "ctype" => "Inbound"])
            ->getQuery()->getSingleScalarResult();
        $rows ["answeredSecond"] = $in20secondAnswered;

        $columns [] = ["data" => "answeredSecond", "name" => "answeredSecond", "title" => "CEVAPLANAN 20(SN)"];

        /////20 sn içinde Kaçan ÇAĞRI SAYISI
        ///
        $in20secondMissedCalls = $callsRepository->createQueryBuilder("cl")
            ->select("count(cl.idx)")
            ->where("cl.dtQueue is not null")
            ->andWhere("cl.exten is null")
            ->andWhere("cl.callStatus=:status")
            ->andWhere("cl.dt between :sDate and :eDate")
            ->andWhere("cl.callType=:ctype")
            ->andWhere("cl.durQueue <= 20")
            ->setParameters(["status" => "Done", "sDate" => $sDate, "eDate" => $eDate, "ctype" => "Inbound"])
            ->getQuery()->getSingleScalarResult();

        $rows ["abandonSecond"] = $in20secondMissedCalls;
        $columns [] = ["data" => "abandonSecond", "name" => "abandonSecond", "title" => "KAÇAN 20(SN)"];

        /////son 15 dakikada 20 sn den kısa surede cevaplanan ÇAĞRI SAYISI
        ///
        $in15min20secCompletedCalls = $callsRepository->createQueryBuilder("cl")
            ->select("count(cl.idx)")
            ->where("cl.exten is not null")
            ->andWhere("cl.callStatus=:status")
            ->andWhere("cl.dt between :sDate and :eDate")
            ->andWhere("cl.callType=:ctype")
            ->andWhere("cl.durQueue <= 20")
            ->setParameters(["status" => "Done", "sDate" => $firstTimeSecond, "eDate" => $lastTimeSecond, "ctype" => "Inbound"])
            ->getQuery()->getSingleScalarResult();

        $columns [] = ["data" => "answeredSecondTwo", "name" => "answeredSecondTwo", "title" => "CEVAPLANAN (15 DK)"];

        /////son 15 dk da GELEN ÇAĞRI SAYISI
        ///
        $in15mintotalCalls = $callsRepository->createQueryBuilder("cl")
            ->select("count(cl.idx)")
            ->where("cl.dt between :sDate and :eDate")
            ->andWhere("cl.callType=:ctype")
            ->andWhere("cl.callStatus=:status")
            ->setParameters(["sDate" => $firstTimeSecond, "eDate" => $lastTimeSecond, "ctype" => "Inbound", "status" => "Done"])
            ->getQuery()->getSingleScalarResult();
        $rows ["callSecond"] = $in15mintotalCalls;
        $columns [] = ["data" => "callSecond", "name" => "callSecond", "title" => "GELEN (15 DK)"];


        /////son 15 dakikada  cevaplanan ÇAĞRI SAYISI
        ///
        $in15minCompletedCalls = $callsRepository->createQueryBuilder("cl")
            ->select("count(cl.idx)")
            ->where("cl.exten is not null")
            ->andWhere("cl.callStatus=:status")
            ->andWhere("cl.dt between :sDate and :eDate")
            ->andWhere("cl.callType=:ctype")
            ->setParameters(["status" => "Done", "sDate" => $firstTimeSecond, "eDate" => $lastTimeSecond, "ctype" => "Inbound"])
            ->getQuery()->getSingleScalarResult();

        $rows ["answeredSecondTwo"] = $in15minCompletedCalls;

        $columns [] = ["data" => "answeredSecondTwo", "name" => "answeredSecondTwo", "title" => "CEVAPLANAN (15 DK)"];


        /////son 15 dakikada   Kaçan ÇAĞRI SAYISI
        ///
        $in15minmissedCalls = $callsRepository->createQueryBuilder("cl")
            ->select("count(cl.idx)")
            ->where("cl.dtQueue is not null")
            ->andWhere("cl.exten is null")
            ->andWhere("cl.callStatus=:status")
            ->andWhere("cl.dt between :sDate and :eDate")
            ->andWhere("cl.callType=:ctype")
            ->setParameters(["status" => "Done", "sDate" => $firstTimeSecond, "eDate" => $lastTimeSecond, "ctype" => "Inbound"])
            ->getQuery()->getSingleScalarResult();

        $rows ["abandonSecondTwo"] = $in15minmissedCalls;

        $columns [] = ["data" => "abandonSecondTwo", "name" => "abandonSecondTwo", "title" => "KAÇAN (15 DK)"];


        /////son 15 dk da 20sn cevaplanan Başarılı ÇAĞRI SAYISI
        ///
        $last15minIn20secondAnswered = $callsRepository->createQueryBuilder("cl")
            ->select("count(cl.idx)")
            ->where("cl.exten is not null")
            ->andWhere("cl.callStatus=:status")
            ->andWhere("cl.dt between :sDate and :eDate")
            ->andWhere("cl.callType=:ctype")
            ->andWhere("cl.durQueue <= 20")
            ->setParameters(["status" => "Done", "sDate" => $firstTimeSecond, "eDate" => $lastTimeSecond, "ctype" => "Inbound"])
            ->getQuery()->getSingleScalarResult();

        $rows ["answeredSecondTree"] = $last15minIn20secondAnswered;

        $columns [] = ["data" => "answeredSecondTree", "name" => "answeredSecondTree", "title" => "CEVAPLANAN 20 SN (15 DK)"];


        /////son 15 dk da 20 sn içinde Kaçan ÇAĞRI SAYISI
        ///
        $last15minIn20secondMissedCalls = $callsRepository->createQueryBuilder("cl")
            ->select("count(cl.idx)")
            ->where("cl.dtQueue is not null")
            ->andWhere("cl.exten is null")
            ->andWhere("cl.callStatus=:status")
            ->andWhere("cl.dt between :sDate and :eDate")
            ->andWhere("cl.callType=:ctype")
            ->andWhere("cl.durQueue <= 20")
            ->setParameters(["status" => "Done", "sDate" => $firstTimeSecond, "eDate" => $lastTimeSecond, "ctype" => "Inbound"])
            ->getQuery()->getSingleScalarResult();
        $rows ["abandonSecondTree"] = $last15minIn20secondMissedCalls;

        $columns [] = ["data" => "abandonSecondTree", "name" => "abandonSecondTree", "title" => "KAÇAN 20 SN (15 DK)"];

        /////bekleme suresinin ortalaması
        ///
        $avgToDurQue = $callsRepository->createQueryBuilder("cl")
            ->select("avg(cl.durQueue)")
            ->where("cl.dt between :sDate and :eDate")
            ->andWhere("cl.callType=:ctype")
            ->andWhere("cl.callStatus=:status")
            ->setParameters(["sDate" => $sDate, "eDate" => $eDate, "ctype" => "Inbound", "status" => "Done"])
            ->getQuery()->getSingleScalarResult();
        $rows ["holdTimeOrt"] = gmdate("H:i:s", $avgToDurQue);

        $columns [] = ["data" => "holdTimeOrt", "name" => "holdTimeOrt", "title" => "BEKLEYEN ORT. (SN)"];

        /////en fazla bekleme suresi
        ///
        $maxToDurQue = $callsRepository->createQueryBuilder("cl")
            ->select("max(cl.durQueue)")
            ->where("cl.dt between :sDate and :eDate")
            ->andWhere("cl.callType=:ctype")
            ->andWhere("cl.callStatus=:status")
            ->setParameters(["sDate" => $sDate, "eDate" => $eDate, "ctype" => "Inbound", "status" => "Done"])
            ->getQuery()->getSingleScalarResult();

        $rows ["holdTimeMax"] = gmdate("H:i:s", $maxToDurQue);

        $columns [] = ["data" => "holdTimeMax", "name" => "holdTimeMax", "title" => "BEKLEYEN MAX (SN)"];


        //TOPLAM ACW SAYISI

        $totalACW = 0;
        foreach ($acwTypes as $acwType) {
            $acwCount = $acwLogsRepository->createQueryBuilder("al")
                ->select("COUNT(al.id)")
                ->andWhere("al.acwType=:acwType")
                ->andWhere("al.endTime is null")
//                ->andWhere("al.startTime BETWEEN :startDate AND :endDate")
                ->setParameters([
//                    "startDate" => $sDate,
//                    "endDate" => $eDate,
                    "acwType" => $acwType
                ])->getQuery()->getSingleScalarResult();

            if ($acwCount == null) {
                $acwCount = 0;
            } else {
                $acwCount = $acwCount + 0;
            }

            $rows [$acwType->getName()] = $acwCount;
            $columns [] = ["data" => $acwType->getName(), "name" => $acwType->getName(), "title" => $acwType->getName()];
            $totalACW += $acwCount;
        }
        $rows ["totalAcw"] = $totalACW;
        $columns [] = ["data" => "totalAcw", "name" => "totalAcw", "title" => "TOPLAM İŞLEM  ADEDİ"];


        ////////////////ACHT////////////////ACHT////////////////ACHT////////////////ACHT////////////////ACHT
        $callsEnterque = $callsRepository->createQueryBuilder('c')
            ->Where('c.dt BETWEEN :startDate AND :endDate')
            ->andWhere('c.callStatus=:callStatus')
            ->andWhere("c.callType=:ctype")
            ->setParameters([
                "startDate" => $sDate,
                "endDate" => $eDate,
                "callStatus" => "Done",
                "ctype" => "Inbound"
            ])
            ->getQuery()->getResult();

        $countCallsEnterque = count($callsEnterque);

        $totaldiffCallTime = 0;
        foreach ($callsEnterque as $callTime) {
            if ($callTime->getDtHangUp() == null) {
                $diffCallTime = Date::diffDateTimeToSecond(new \DateTime(), $callTime->getDtExten());
            } else {
//                $diffCallTime = $callTime->getDtHangUp()->getTimeStamp() - $callTime->getDtExten()->getTimeStamp();
                $diffCallTime = $callTime->getDurExten();
            }
            $totaldiffCallTime += $diffCallTime;
        }
        $totalCallTimeResult = gmdate("H:i:s", $totaldiffCallTime);
        $totalCallTimeResultLast = $totalCallTimeResult;


        $holdLogs = $holdLogRepo->createQueryBuilder("hl")
            ->andWhere("hl.startTime BETWEEN :startDate AND :endDate")
            ->setParameters([
                "startDate" => $sDate,
                "endDate" => $eDate,
            ])
            ->getQuery()->getResult();

        $totaldiffHoldTime = 0;
        foreach ($holdLogs as $holdLog) {
            if ($holdLog->getEndTime() == null) {
                $diffHoldTime = Date::diffDateTimeToSecond(new \DateTime(), $holdLog->getStartTime());
            } else {
                $diffHoldTime = Date::diffDateTimeToSecond($holdLog->getEndTime(), $holdLog->getStartTime());
            }
            $totaldiffHoldTime += $diffHoldTime;
        }
        $totalHoldTimeResult = gmdate("H:i:s", $totaldiffHoldTime);
        $totalHoldTimeResultLast = $totalHoldTimeResult;


        $acht = gmdate("H:i:s", ($countCallsEnterque ? ($totaldiffCallTime + $totaldiffHoldTime) / $countCallsEnterque : 0));

        $rows ["acht"] = $acht;
        $columns [] = ["data" => "acht", "name" => "acht", "title" => "ACHT"];

////////////////ACHT15////////////////ACHT15////////////////ACHT15////////////////ACHT15

        $callsEnterque15 = $callsRepository->createQueryBuilder('c')
            ->where("c.exten is not null")
            ->andWhere('c.dt BETWEEN :startDate AND :endDate')
            ->andWhere('c.callStatus=:callStatus')
            ->andWhere("c.callType=:ctype")
            ->setParameters([
                "startDate" => $firstTimeSecond,
                "endDate" => $lastTimeSecond,
                "callStatus" => "Done",
                "ctype" => "Inbound"
            ])
            ->getQuery()->getResult();

        $countCallsEnterque15 = count($callsEnterque15);

        $totaldiffCallTime15 = 0;
        foreach ($callsEnterque15 as $callTime15) {
            if ($callTime15->getDtHangUp() == null) {
                $diffCallTime15 = Date::diffDateTimeToSecond(new \DateTime(), $callTime15->getDtExten());
            } else {
//                $diffCallTime = $callTime->getDtHangUp()->getTimeStamp() - $callTime->getDtExten()->getTimeStamp();
                $diffCallTime15 = $callTime15->getDurExten();
            }
            $totaldiffCallTime15 += $diffCallTime15;
        }
        $totalCallTimeResult15 = gmdate("H:i:s", $totaldiffCallTime15);
        $totalCallTimeResultLast15 = $totalCallTimeResult15;


        $holdLogs15 = $holdLogRepo->createQueryBuilder("hl")
            ->andWhere("hl.startTime BETWEEN :startDate AND :endDate")
            ->setParameters([
                "startDate" => $firstTimeSecond,
                "endDate" => $lastTimeSecond,
            ])
            ->getQuery()->getResult();

        $totaldiffHoldTime15 = 0;
        foreach ($holdLogs15 as $holdLog15) {
            if ($holdLog15->getEndTime() == null) {
                $diffHoldTime15 = Date::diffDateTimeToSecond(new \DateTime(), $holdLog15->getStartTime());
            } else {
                $diffHoldTime15 = Date::diffDateTimeToSecond($holdLog15->getEndTime(), $holdLog15->getStartTime());
            }
            $totaldiffHoldTime15 += $diffHoldTime15;
        }
        $totalHoldTimeResult15 = gmdate("H:i:s", $totaldiffHoldTime15);
        $totalHoldTimeResultLast15 = $totalHoldTimeResult15;

////////////////ACHT15////////////////ACHT15////////////////ACHT15////////////////ACHT15

        $acht15 = gmdate("H:i:s", ($countCallsEnterque15 ? ($totaldiffCallTime15 + $totaldiffHoldTime15) / $countCallsEnterque15 : 0));
        $rows ["acht15"] = $acht15;
        $columns [] = ["data" => "acht15", "name" => "acht15", "title" => "ACHT15dk"];


//        $rows = [];
//
//        $rows ["loginCount"] = $lgnCount;
//        $rows ["summaryTotalLoginTimeResult"] = $summaryTotalLoginTimeResult;
//
//        $rows ["talkingInbound"] = $talkingInbound;
//        $rows ["talkingOutbound"] = $talkingOutbound;
//        $rows ["totalBreakCount"] = $totalBreakCount;
//        $rows ["totalReadyCount"] = $totalReadyCount;
//        $rows ["totalNotReadyCount"] = $totalNotReadyCount;
//
//
////        $rows ["columnsDataTable"] = $columnsDataTable;
//
//
//        $rows ["call"] = $totalCalls;
//        $rows ["answered"] = $completedCalls;
//        $rows ["outboundCalls"] = $outboundCalls;
//        $rows ["abandon"] = $missedCalls;
//        $rows ["toIvrEnded"] = $toIvrEndedCalls;
//        $rows ["inwaitingCalls"] = $activeCalls;
//        $rows ["answeredSecond"] = $in20secondAnswered;
//        $rows ["abandonSecond"] = $in20secondMissedCalls;
//
//        $serviceLevel = $completedCalls ? $in20secondAnswered / $completedCalls : 0;
//        $rows ["serviceLevel"] = "%" . (round($serviceLevel, 2) * 100);
//
//        $connectivity = $totalCalls ? $completedCalls / $totalCalls : 0;
//        $rows ["connectivity"] = "%" . (round($connectivity, 2) * 100);
//
//        $rows ["callSecond"] = $in15mintotalCalls;
//        $rows ["answeredSecondTwo"] = $in15minCompletedCalls;
//        $rows ["abandonSecondTwo"] = $in15minmissedCalls;
//        $rows ["answeredSecondTree"] = $last15minIn20secondAnswered;
//        $rows ["abandonSecondTree"] = $last15minIn20secondMissedCalls;
//
//        $serviceLevelin15min = $in15minCompletedCalls ? $in15min20secCompletedCalls / $in15minCompletedCalls : 0;
//        $rows ["serviceLevelSecond"] = "%" . (round($serviceLevelin15min, 2) * 100);
//
//        $connectivity15min = $in15mintotalCalls ? $in15minCompletedCalls / $in15mintotalCalls : 0;
//        $rows ["connectivitySecond"] = "%" . (round($connectivity15min, 2) * 100);
//
//        $rows ["holdTimeOrt"] = gmdate("H:i:s", $avgToDurQue);
//        $rows ["holdTimeMax"] = gmdate("H:i:s", $maxToDurQue);
//        $rows ["acht"] = $acht;
//        $rows ["acht15"] = $acht15;


//        $columns = [
//            ["data" => "loginCount", "name" => "loginCount", "title" => "TOPLAM LOGİN SAYISI"],
//            ["data" => "summaryTotalLoginTimeResult", "name" => "summaryTotalLoginTimeResult", "title" => "TOPLAM LOGİN SÜRESİ"],
//            ["data" => "talkingInbound", "name" => "talkingInbound", "title" => "TALKING INBOUND"],
//            ["data" => "talkingOutbound", "name" => "talkingOutbound", "title" => "TALKING OUTBOUND"],
//
//            ["data" => "totalBreakCount", "name" => "totalBreakCount", "title" => "MOLA"],
//            ["data" => "totalReadyCount", "name" => "totalReadyCount", "title" => "Hazır Olanlar"],
//            ["data" => "totalNotReadyCount", "name" => "totalNotReadyCount", "title" => "Hazır Olmayanlar"],
//
//
////            ["data" => $acwType->getName(), "name" =>$acwType->getName(), "title" =>$acwType->getName()],
//
//
//            ["data" => "call", "name" => "call", "title" => "GELEN"],
//            ["data" => "outboundCalls", "name" => "outboundCalls", "title" => "OUTBOUND ÇAGRI SAYISI"],
//            ["data" => "answered", "name" => "answered", "title" => "CEVAPLANAN"],
//            ["data" => "abandon", "name" => "abandon", "title" => "KAÇAN"],
//            ["data" => "toIvrEnded", "name" => "toIvrEnded", "title" => "IVRDA TAMAMLANAN"],
//            ["data" => "inwaitingCalls", "name" => "inwaitingCalls", "title" => "BEKLEYEN"],
//            ["data" => "answeredSecond", "name" => "answeredSecond", "title" => "CEVAPLANAN 20(SN)"],
//            ["data" => "abandonSecond", "name" => "abandonSecond", "title" => "KAÇAN 20(SN)"],
//            ["data" => "serviceLevel", "name" => "serviceLevel", "title" => "SERVİS LEVEL"],
//            ["data" => "connectivity", "name" => "connectivity", "title" => "CEVAPLANMA ORANI"],
//            ["data" => "callSecond", "name" => "callSecond", "title" => "GELEN (15 DK)"],
//            ["data" => "answeredSecondTwo", "name" => "answeredSecondTwo", "title" => "CEVAPLANAN (15 DK)"],
//            ["data" => "abandonSecondTwo", "name" => "abandonSecondTwo", "title" => "KAÇAN (15 DK)"],
//            ["data" => "answeredSecondTree", "name" => "answeredSecondTree", "title" => "CEVAPLANAN 20 SN (15 DK)"],
//            ["data" => "abandonSecondTree", "name" => "abandonSecondTree", "title" => "KAÇAN 20 SN (15 DK)"],
//            ["data" => "serviceLevelSecond", "name" => "serviceLevelSecond", "title" => "SERVİS LEVEL (15 DK)"],
//            ["data" => "connectivitySecond", "name" => "connectivitySecond", "title" => "CEVAPLANMA ORANI (15 DK)"],
//            ["data" => "holdTimeOrt", "name" => "holdTimeOrt", "title" => "BEKLEYEN ORT. (SN)"],
//            ["data" => "holdTimeMax", "name" => "holdTimeMax", "title" => "BEKLEYEN MAX (SN)"],
//            ["data" => "acht", "name" => "acht", "title" => "ACHT"],
//            ["data" => "acht15", "name" => "acht15", "title" => "ACHT15dk"],
//        ];

        return $this->json(["columns" => $columns, "datas" => [$rows]]);
    }
}