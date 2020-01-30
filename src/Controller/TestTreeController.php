<?php
/**
 * Created by PhpStorm.
 * User: aydn33
 * Date: 6.02.2019
 * Time: 16:43
 */

namespace App\Controller;

use App\Asterisk\Entity\CustomShortcuts;
use App\Asterisk\Entity\Queues;
use App\Asterisk\Entity\Ivr;
use App\Asterisk\Entity\IvrLogs;
use App\Entity\AcwLog;
use App\Entity\AcwType;
use App\Entity\AgentBreak;
use App\Entity\BreakType;
use App\Entity\Calls;
use App\Entity\Evaluation;
use App\Entity\EvaluationResetReason;
use App\Entity\EvaluationSource;
use App\Entity\Group;
use App\Entity\HoldLog;
use App\Entity\LoginLog;
use App\Entity\QueuesMembersDynamic;
use App\Entity\RealtimeQueueMembers;
use App\Entity\RegisterLog;
use App\Entity\Role;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\UserLog;
use App\Entity\UserProfile;
use App\Entity\UserSkill;
use App\Entity\IvrServiceLog;
use App\EventListener\LoginListener;
use App\Form\Group1Type;
use App\Form\GroupType;
use App\Helpers\Date;
use App\Repository\EvaluationResetReasonRepository;
use function r\ne;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;


class TestTreeController extends AbstractController
{
    /**
     * @Route("/agent/logout/{user}")
     * @param User $user
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function logoutUSer(User $user)
    {
        $userRepo = $this->getDoctrine()->getRepository(User::class)->logoutUser($user);
        return $this->json(["ok"]);
    }
    /**
     * @Route("/adasdas")
     */
    public function loginlogtest()
    {


        $em = $this->getDoctrine()->getManager();
            $id=94;
        $resetReason = $em->getRepository(EvaluationResetReason::class)->find($id);
        if(!is_null($resetReason))
        {
            $resetReason
                    ->setForms([13,15,21,157,166,226,231,232,235,236,237,238,239,240,241,242,243,244,250,251,252,253,254,255,256,258,260,259,261,262,263]);
                $em->persist($resetReason);
                $em->flush();
        }

        return $this->json("Basarili");
    }


    private $columnsControl;

    public function __construct()
    {
        $this->columnsControl = false;
    }

    /**
     * @Route("/asdasd/{group}")
     * @param UserInterface $user
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function adsda(Request $request, Group $group)
    {

//        $group = new Group('');

        $roleArray = $this->getDoctrine()->getRepository(Role::class)->createQueryBuilder('r')
            ->getQuery()->getArrayResult();

        $roles = array_column($roleArray, "id", "title");


        $form = $this->createForm(GroupType::class, $group, ['roles' => $roles]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $formData = $request->request->get("group");

            $group->setRoles($formData['roles']);
            $entityManager->persist($group);
            $entityManager->flush();

            return $this->redirectToRoute('group_index');
        }


        return $this->render('group/new.html.twig', [
            'group' => $group,
            'form' => $form->createView(),
        ]);

        exit();


        $em = $this->getDoctrine()->getManager();
        $newdate = new \DateTime();

        $lastTimeSecond = $newdate->format("Y-m-d H:i:s");
        $firstTimeSecond = $newdate->modify("-15 minute")->format("Y-m-d H:i:s");

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

        dump($acwTypesRepository);
        exit();
//        $row = [];
//        $row ["dateRange"] = "";
//        $row ["dateRangeTime"] = "";
//        $row ["agentTc"] = "";
//        $row ["agent"] = "";
//        $columns = [];
//        if ($this->columnsControl == false) {
//            $columns [] = ["data" => "dateRange", "name" => "dateRange", "title" => "TARİH"];
//            $columns [] = ["data" => "dateRangeTime", "name" => "dateRangeTime", "title" => "24/15 <br> DAKİKALIK <br> VEYA SAATLİK"];
//            $columns [] = ["data" => "agentTc", "name" => "agentTc", "title" => "TC NO"];
//            $columns [] = ["data" => "agent", "name" => "agent", "title" => "PERSONEL"];
//        }

        $totalACW = 0;
        foreach ($acwTypesRepository as $acwType) {
            dump($acwTypesRepository);
            dump($acwType);
            exit();
            $acwSum = $acwLogsRepository->createQueryBuilder("al")
                ->select("COUNT(al.id)")
                ->andWhere("al.acwType=:acwType")
                ->andWhere("al.startTime BETWEEN :startDate AND :endDate")
                ->setParameters([
                    "startDate" => $sDate,
                    "endDate" => $eDate,
                    "acwType" => $acwType
                ])->getQuery()->getResult();

            if ($acwSum == null) {
                $acwSum = 0;
            } else {
                $acwSum = $acwSum + 0;
            }

            $row [$acwType->getName()] = $acwSum;
            $columns [] = ["data" => $acwType->getName(), "name" => $acwType->getName(), "title" => $acwType->getName()];
            $totalACW += $acwSum;


        }
//        dump($acwType);
//        dump($acwLogsRepository);
//        dump($columns);
//        exit();
        $row ["totalAcw"] = gmdate("H:i:s", $totalACW);
        $columns [] = ["data" => "totalAcw", "name" => "totalAcw", "title" => "TOPLAM İŞLEM  ADEDİ"];
//dump($columns);
//exit();


        $callsEnterque = $callsRepository->createQueryBuilder('c')
//        ->Where('c.dt BETWEEN :startDate AND :endDate')
            ->andWhere('c.callStatus=:callStatus')
            ->andWhere("c.callType=:ctype")
            ->setParameters([
//            "startDate" => $sDate,
//            "endDate" => $eDate,
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
        $totalCallTimeResult = $totaldiffCallTime;
//        $totalCallTimeResultLast = $totalCallTimeResult;


        $holdLogs = $holdLogRepo->createQueryBuilder("hl")
//            ->andWhere("hl.startTime BETWEEN :startDate AND :endDate")
            ->setParameters([
//                "startDate" => $sDate,
//                "endDate" => $eDate,
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
        $totalHoldTimeResult = $totaldiffHoldTime;
//        $totalHoldTimeResultLast = $totalHoldTimeResult;

////////////////ACHT

        $acht = gmdate("H:i:s", ($countCallsEnterque ? ($totalCallTimeResult + $totalHoldTimeResult) / $countCallsEnterque : 0));

        dump($acht);
        exit();
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


////////////////ACHT

        $acht15 = gmdate("H:i:s", ($countCallsEnterque15 ? ($totalCallTimeResultLast15 + $totalHoldTimeResultLast15) / $countCallsEnterque15 : 0));

        dump($acht15);
        exit();
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//        $callsEnterque = $callsRepository->createQueryBuilder('c')
//            ->Where('c.dt BETWEEN :startDate AND :endDate')
//            ->andWhere('c.callStatus=:callStatus')
//            ->andWhere("c.callType=:ctype")
//            ->setParameters([
//                "startDate" => $sDate,
//                "endDate" => $eDate,
//                "callStatus" => "Done",
//                "ctype" => "Inbound"
//            ])
//            ->getQuery()->getResult();
//
//        $countCallsEnterque = count($callsEnterque);
//
//        $totaldiffCallTime = 0;
//        foreach ($callsEnterque as $callTime) {
//            if ($callTime->getDtHangUp() == null) {
//                $diffCallTime = Date::diffDateTimeToSecond(new \DateTime(), $callTime->getDtExten());
//            } else {
////                $diffCallTime = $callTime->getDtHangUp()->getTimeStamp() - $callTime->getDtExten()->getTimeStamp();
//                $diffCallTime = $callTime->getDurExten();
//            }
//            $totaldiffCallTime += $diffCallTime;
//        }
//        $totalCallTimeResult = gmdate("H:i:s", $totaldiffCallTime);
//        $totalCallTimeResultLast = $totalCallTimeResult;
//
//
//        $holdLogs = $holdLogRepo->createQueryBuilder("hl")
//            ->andWhere("hl.startTime BETWEEN :startDate AND :endDate")
//            ->setParameters([
//                "startDate" => $sDate,
//                "endDate" => $eDate,
//            ])
//            ->getQuery()->getResult();
//
//        $totaldiffHoldTime = 0;
//        foreach ($holdLogs as $holdLog) {
//            if ($holdLog->getEndTime() == null) {
//                $diffHoldTime = Date::diffDateTimeToSecond(new \DateTime(), $holdLog->getStartTime());
//            } else {
//                $diffHoldTime = Date::diffDateTimeToSecond($holdLog->getEndTime(), $holdLog->getStartTime());
//            }
//            $totaldiffHoldTime += $diffHoldTime;
//        }
//        $totalHoldTimeResult = gmdate("H:i:s", $totaldiffHoldTime);
//        $totalHoldTimeResultLast = $totalHoldTimeResult;
//
//////////////////ACHT
//
//        $acht= gmdate("H:i:s", ($countCallsEnterque ? ($totalCallTimeResultLast + $totalHoldTimeResultLast) / $countCallsEnterque : 0));
//        dump($acht);
//        exit();
//
//
//
//
//        $holdLogs = $holdLogRepo->createQueryBuilder("hl")
//            ->andWhere("hl.startTime BETWEEN :startDate AND :endDate")
//            ->setParameters([
//                "startDate" => $sDate,
//                "endDate" => $eDate,
//            ])
//            ->getQuery()->getResult();
//
//        $totaldiffHoldTime = 0;
//        foreach ($holdLogs as $holdLog) {
//            if ($holdLog->getEndTime() == null) {
//                $diffHoldTime = Date::diffDateTimeToSecond(new \DateTime(), $holdLog->getStartTime());
//            } else {
//                $diffHoldTime = Date::diffDateTimeToSecond($holdLog->getEndTime(), $holdLog->getStartTime());
//            }
//            $totaldiffHoldTime += $diffHoldTime;
//        }
//
//        $strTotaldiffCallTime=strtotime($totaldiffHoldTime);
//        $totalStr=$strTotaldiffCallTime + $strTotaldiffCallTime;
//        $dateTotal = date("d-m-Y",strtotime($totalStr));
//        dump($dateTotal);
//        exit();
////        $totalCallTimeResult = gmdate("H:i:s", $totalStr);
////        $totalHoldTimeResult = gmdate("H:i:s", $totaldiffHoldTime);
////


//
//        $rqmRepo = $em->getRepository(RealtimeQueueMembers::class);
//
//        $columnsArr = [];
//        $columnsDataTable = [];
//        foreach ($acwTypesRepository as $acwType) {
//            $acwSum = $acwLogsRepository->createQueryBuilder("al")
//                ->select("COUNT(al.id)")
//                ->where("al.user=:user")
//                ->andWhere("al.acwType=:acwType")
//                ->andWhere("al.startTime BETWEEN :startDate AND :endDate")
//                ->setParameters([
////                    "user" => $userId,
////                    "startDate" => $sDate,
////                    "endDate" => $eDate,
//                    "acwType" => $acwType
//                ])->getQuery()->getSingleScalarResult();
//
//            if ($acwSum == null) {
//                $acwSum = 0;
//            } else {
//                $acwSum = $acwSum + 0;
//            }
//
//            $columnsArr [$acwType->getName()] =$acwSum;
////            if ($columnsDataTableControl == true) {
////            $columnsDataTable [] = ["data" => $acwType->getName(), "name" => $acwType->getName(), "title" => $acwType->getName()];
////            }
//
//
//        }
//        dump($columnsDataTable);
//        exit();
//
//
//        $em = $this->getDoctrine()->getManager();
//        $ab = $em->getRepository(AgentBreak::class);
//
//        $em = $this->getDoctrine()->getManager();
//        $rqmRepo = $em->getRepository(User::class);
//        $inBreak = $rqmRepo->createQueryBuilder("rqm")
//            ->select("COUNT(rqm.id)")
//            ->andWhere("rqm.state =:state")
//            ->setParameters([
//                "state" => 4,
//            ])
//            ->getQuery()->getSingleScalarResult();
//
//        dump($inBreak);
//        exit();
//        $abb = $ab->createQueryBuilder("t")
//            ->select("COUNT(t.idx)")
//            ->where("t.callStatus =:callStatus")
//            ->andWhere("t.callType =:callType")
////            ->andWhere("l.StartTime BETWEEN :sDate AND :eDate")
//            ->setParameters([
//                "callStatus" => "Active",
//                "callType" => "Inbound",
////                "sDate"=> $sDate,
////                "eDate"=> $eDate,
//            ])
//            ->getQuery()->getSingleScalarResult();
//        dump($talkingInbound);
//        exit();
//
//        $publicTotalLoginTimes = $logins->createQueryBuilder("l")
//            ->where("l.EndTime is not null")
////            ->andWhere("l.StartTime BETWEEN :sDate AND :eDate")
////            ->setParameters([
//////                "sDate"=> $sDate,
//////                "eDate"=> $eDate,
////            ])
//            ->getQuery()->getResult();
//        $summaryTotalLoginTime = 0;
//        foreach ($publicTotalLoginTimes as $publicTotalLoginTime) {
////            dump($publicTotalLoginTime);
////            exit();
//            $diff = Date::diffDateTimeToSecond($publicTotalLoginTime->getEndTime(), $publicTotalLoginTime->getStartTime());
//            $summaryTotalLoginTime += $diff;
//        }
//        $summaryTotalLoginTimeResult = gmdate("H:i:s", $summaryTotalLoginTime);
//        dump($summaryTotalLoginTimeResult);
//        exit();
//
//
//        $callsRepository = $em->getRepository(Calls::class);
//        $publicTotalCalls = $callsRepository->createQueryBuilder("cl")
//            ->select("AVG(cl.durExten)")
////            ->Where("cl.dt between :sDate and :eDate")
//            ->andWhere("cl.callType =:ctype")
//            ->setParameters([
//
////                "sDate" => $sDate,
////                "eDate" => $eDate,
//                "ctype" => "Inbound"
//            ])
//            ->getQuery()->getResult();
//        dump($publicTotalCalls);
//        exit();
//
//        /////AGENT GÖRÜŞME ADEDİ
//        $columnsArr ["publicTotalCall"] = count($publicTotalCalls);
//        if ($columnsDataTableControl == true) {
//            $columnsDataTable [] = ["data" => "publicTotalCall", "name" => "publicTotalCall", "title" => "Genel Toplam Çağrı Adedi"];
//        }
//        dump($rqm);
//        exit();
//
//
//        $users = $em->getRepository(UserProfile::class)->findAll();
//        foreach ($users as $user) {
//            $loginCounts = $em->getRepository(LoginLog::class)->createQueryBuilder("rl")
//                ->select("COUNT(rl.id)")
//                ->where("rl.userId =:user")
//                ->andWhere("rl.EndTime is NULL")
//                ->setParameter("user", $user)
//                ->getQuery()->getSingleScalarResult();
//            dump($loginCounts);
//            exit();
//        }
//
//
//        $em = $this->getDoctrine()->getManager();
//        $teamRepo = $em->getRepository(Team::class);
//        $evaList = $em->getRepository(Evaluation::class);
//        $evaQuery = $evaList->createQueryBuilder("eva");
//        $evaQuery
//            ->leftJoin("eva.user", "u")
//            ->where(
//                $evaQuery->expr()->in(
//                    "u.teamId",
//                    $teamRepo->createQueryBuilder("t")
//                        ->select("t.id")
//                        ->where("t.manager=:manager")
//                        ->orWhere("t.managerBackup=:managerBackup")
//                        ->getQuery()->getDQL()
//                )
//            )
//            ->orWhere("eva.user=:user")
//            ->setParameters([
//                "manager" => $user,
//                "managerBackup" => $user,
//                "user" => $user,
//            ]);
//        $evaQuery = $evaQuery->getQuery()->getResult();
//
//        dump($user->getUserProfile());
//        dump($evaQuery);
//        exit();
    }

    /**
     * @Route("/testTree", name="test_tree")
     * @param Request $request
     */
    public function testTree(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $calls = $em->getRepository(Calls::class)
            ->createQueryBuilder('c')
            ->where("c.callStatus=:callStatus")
            ->andWhere("c.callType=:callType")
            ->andWhere("c.durExten <:durExten")
            ->andWhere("c.queue IS NOT NULL")
            ->andWhere("c.user IS NOT NULL")
            ->andWhere("c.whoCompleted IS NOT NULL")
            ->setParameter("callStatus", "Done")
            ->setParameter("callType", "Inbound")
            ->setParameter("durExten", 8)
            ->getQuery()->getResult();
        dump($calls);
        exit();
        $row = [];

        foreach ($calls as $call) {
            $arayan = $call->getClid();
            $aranan = $call->getDid();
//            $temsilci = $call->getExten();
            $tcNo = $call->getUser()->getUserProfile()->getTckn();
            $name = $call->getUser()->getUserProfile()->__toString();
            $konusmaSuresi = $call->getDurExten();
            $whoClosed = $call->getwhoCompleted();
            if ($whoClosed == "COMPLATECALLER") {

                $row[] = [
                    "dateRangeTime" => " ",
                    "arayan" => $arayan,
                    "aranan" => $aranan,
//                    "temsilci" => $temsilci,
                    "tckn" => $tcNo,
                    "name" => $name,
                    "konusmaSuresi" => $konusmaSuresi,
                    "whoclosed" => "Vatandas"
                ];
            } else {
                $row[] = [
                    "dateRange" => " ",
                    "dateRangeTime" => " ",
                    "arayan" => $arayan,
                    "aranan" => $aranan,
//                    "temsilci" => $temsilci,
                    "tckn" => $tcNo,
                    "name" => $name,
                    "konusmaSuresi" => $konusmaSuresi,
                    "whoclosed" => "Temsilci"
                ];
            }
        }
        dump($row);
        exit();
    }
}
//        $em = $this->getDoctrine()->getManager();
//        $calls = $em->getRepository(Calls::class)
//            ->createQueryBuilder('c')
//            ->where("c.callStatus=:callStatus")
//            ->andWhere("c.callType=:callType")
//            ->andWhere("c.durExten <:durExten")
//            ->andWhere("c.queue IS NOT NULL")
//            ->andWhere("c.user IS NOT NULL")
//            ->andWhere("c.whoCompleted =:whoCompleted")
////            ->andwhere('c.dt BETWEEN :startDate AND :endDate')
//            ->setParameter("callStatus", "Done")
//            ->setParameter("callType", "Inbound")
//            ->setParameter("durExten", 8)
//            ->setParameter("whoCompleted", "COMPLETECALLER")
//            ->setParameter("whoCompleted", "COMPLETEAGENT")
////            ->setParameter('startDate', $firstTime)
////            ->setParameter('endDate', $lastTime)
//            ->getQuery()->getResult();
//
//        $row = [];
//
//        foreach ($calls as $call) {
//            $arayan = $call->getClid();
//            $aranan = $call->getDid();
//            $temsilci = $call->getExten();
//            $tcNo = $call->getUser()->getUserProfile()->getTckn();
//            $name = $call->getUser()->getUserProfile()->__toString();
//            $konusmaSuresi = $call->getDurExten();
//            $whoClosed = $call->getwhoCompleted();
//            dump($whoClosed);
//            exit();
//
//            if ($whoClosed == "COMPLATECALLER") {
//
//                $row[] = [
//                    "dateRangeTime" => " ",
//                    "arayan" => $arayan,
//                    "aranan" => $aranan,
////                    "temsilci" => $temsilci,
//                    "tckn" => $tcNo,
//                    "name" => $name,
//                    "konusmaSuresi" => $konusmaSuresi,
//                    "whoclosed" => "Vatandas"
//                ];
//            }
//            else {
//                $calls = $em->getRepository(Calls::class)
//                    ->createQueryBuilder('c')
//                    ->where("c.callStatus=:callStatus")
//                    ->andWhere("c.callType=:callType")
//                    ->andWhere("c.durExten <:durExten")
//                    ->andWhere("c.queue IS NOT NULL")
//                    ->andWhere("c.user IS NOT NULL")
//                    ->andWhere("c.whoCompleted =:whoCompleted")
////            ->andwhere('c.dt BETWEEN :startDate AND :endDate')
//                    ->setParameter("callStatus", "Done")
//                    ->setParameter("callType", "Inbound")
//                    ->setParameter("durExten", 8)
//                    ->setParameter("whoCompleted", "COMPLETEAGENT")
////            ->setParameter('startDate', $firstTime)
////            ->setParameter('endDate', $lastTime)
//                    ->getQuery()->getResult();
//                $row = [];
//                foreach ($calls as $call) {
//                    $arayan = $call->getClid();
//                    $aranan = $call->getDid();
//                    $temsilci = $call->getExten();
//                    $tcNo = $call->getUser()->getUserProfile()->getTckn();
//                    $name = $call->getUser()->getUserProfile()->__toString();
//                    $konusmaSuresi = $call->getDurExten();
//                    $whoClosed = $call->getwhoCompleted();
//                    $row[] = [
//                        "dateRangeTime" => " ",
//                        "arayan" => $arayan,
//                        "aranan" => $aranan,
////                    "temsilci" => $temsilci,
//                        "tckn" => $tcNo,
//                        "name" => $name,
//                        "konusmaSuresi" => $konusmaSuresi,
//                        "whoclosed" => "Vatandas"
//                    ];
//                }
//                dump($call);
//                exit();
//            }

//
//        }
//    }
//}

//        $row = [];
//
//        foreach ($calls as $call) {
//            $arayan = $call->getClid();
//            $aranan = $call->getDid();
////            $temsilci = $call->getExten();
//            $tcNo = $call->getUser()->getUserProfile()->getTckn();
//            $name = $call->getUser()->getUserProfile()->__toString();
//            $konusmaSuresi = $call->getDurExten();
//            $whoClosed = $call->getwhoCompleted();
//            if ($whoClosed == "COMPLATECALLER") {
//
//                $row[] = [
//                    "dateRangeTime" => " ",
//                    "arayan" => $arayan,
//                    "aranan" => $aranan,
////                    "temsilci" => $temsilci,
//                    "tckn" => $tcNo,
//                    "name" => $name,
//                    "konusmaSuresi" => $konusmaSuresi,
//                    "whoclosed" => "Vatandas"
//                ];
//            }
//            else {
//                $row[] = [
//                    "dateRange" => " ",
//                    "dateRangeTime" => " ",
//                    "arayan" => $arayan,
//                    "aranan" => $aranan,
////                    "temsilci" => $temsilci,
//                    "tckn" => $tcNo,
//                    "name" => $name,
//                    "konusmaSuresi" => $konusmaSuresi,
//                    "whoclosed" => "Temsilci"
//                ];
//            }
//        }
//            dump($calls);
//            exit();
//        }
//    }
//}
//        $em = $this->getDoctrine()->getManager();
//        $acwTypesRepository = $em->getRepository(AcwType::class);
//
////        $user=$em->getRepository(User::class);
//
//        foreach ($acwTypesRepository as $acwType) {
//            dump($acwType);
//            exit();
//
//        }
//    }
//        $acwTypes = $user->createQueryBuilder("u")
//            ->select("COUNT(u.id)")
////            ->leftJoin("rqm.user","u")
//            ->andwhere("u.state=:state")
//            ->setParameters([
//                "state"=>2,
//            ])->getQuery()->getSingleScalarResult();
//


//
//
//        $newdate = new \DateTime();
//        $sDate = $newdate->format("Y-m-d 00:00:00");
//        $eDate = $newdate->format("Y-m-d 23:59:59");
//        $agentBreakRepository = $em->getRepository(AgentBreak::class);
//        $acwTypesRepository = $em->getRepository(AcwType::class);
//        $acwLogsRepository = $em->getRepository(AcwLog::class);
//        $queueRepository = $em->getRepository(Queues::class);
//        $queueMemberDynamicRepository = $em->getRepository(QueuesMembersDynamic::class);
//        $userProfileRepository = $em->getRepository(UserProfile::class);
//        $realtimeQueMember = $em->getRepository(RealtimeQueueMembers::class);
//        $callsRepository = $em->getRepository(Calls::class);
//
////        $client = new \GuzzleHttp\Client();
////        $queueSummarys = $client->request('GET', 'https://'.$this->getParameter('tbxSipServer').'/api/queue_summary.txt');
////        $queueSummarys = $queueSummarys->getBody()->getContents();
////        $queueSummarys = json_decode($queueSummarys,true);
//
//        $queues = $queueRepository->createQueryBuilder("q")
//            ->select("q.queue,q.description")
//            ->getQuery()
//            ->getArrayResult();
//
//        $result = [];
//        foreach ($queues as $queue) {
//
//            $rqmRepo = $em->getRepository(RealtimeQueueMembers::class);
//            $inBreak = $rqmRepo->createQueryBuilder("rqm")
//                ->select("COUNT(rqm.uniqueid)")
//                ->where("rqm.paused=:paused")
//                ->leftJoin("rqm.user","u")
//                ->andwhere("u.state=:state")
//                ->andWhere("rqm.queueName=:queueName")
//                ->setParameters([
//                    "paused"=>1,
//                    "state"=>4,
//                    "queueName"=>$queue["queue"]
//                ])->getQuery()->getSingleScalarResult();
//
//
//
//            $rqmRepo = $em->getRepository(RealtimeQueueMembers::class);
//            $inAcw = $rqmRepo->createQueryBuilder("rqm");
//            $inAcw
//                ->select("COUNT(rqm.uniqueid)")
//                ->leftJoin("rqm.user", "u")
//                ->where(
//                    $inAcw->expr()->in(
//                        "u.state", [2, 5, 6, 11]
//                    )
//                )
//                ->andWhere("rqm.queueName=:queueName")
//                ->andWhere("rqm.paused=:paused")
//                ->setParameters([
//                    "queueName" => $queue["queue"],
//                    "paused" => 1
//                ]);
//            $inAcw = $inAcw->getQuery()->getSingleScalarResult();
//
//            dump($inAcw);
//            exit();
//        }


//        $s = $em = $this->getDoctrine()->getManager()->getRepository(EvaluationSource::class)->createQueryBuilder("s")->getQuery()->getArrayResult();
//
//        return $this->json($s);
//        exit;
//
//
//        $eva = $this->getDoctrine()->getRepository(Evaluation::class)->createQueryBuilder("e")
//            ->where("e.createdAt BETWEEN :start AND :end")
//            ->setParameters(["start" => "2019-04-04 00:00:00", "end" => "2019-04-05 23:59:59"])
//            ->getQuery()->getResult();
//
//        dump($eva);
//        exit();
//
//
//        $em = $this->getDoctrine()->getManager();
//
//        $aa = $em->getRepository(UserLog::class)->findAll();
//
//        dump($aa);

//        $acw = $this->getDoctrine()->getRepository(AcwLog::class)->findAll();
//        $team = $this->getDoctrine()->getRepository(Team::class)->findAll();

//        dump($acw);
//        dump($team);

//        exit;
//        $client = new \GuzzleHttp\Client();
//        $response = $client->request('GET', 'http://10.5.95.157/stream.php?uid=20190402-163318-02122953275-9341001710-667201-1554211997.5941');
//        $content =  $response->getBody()->getContents();
//
//        header('Content-type: audio/mpeg');
//        header('Content-length: ' . strlen($content));
//        header('X-Pad: avoid browser bug');
//        Header('Cache-Control: no-cache');
//        header("Content-Transfer-Encoding: binary");
//        header("Content-Type: audio/mpeg, audio/x-mpeg, audio/x-mpeg-3, audio/mpeg3");
//        header('Content-Disposition: filename="testingen.mp3"');
//
//        echo $content;
//        exit;

//        $tineString = "2019-10-05 02:10:10";
//        $tine = date("Y-m-d H:i:s",strtotime("-1 year, -1 month, -3 day, -1 hours, -1 minute, -1 second",strtotime($tineString)));
//        $tineInteger = strtotime($tine);
//
//        dump($tine);
//        dump($tineInteger);

//        function aradancek($bununla,$bunun,$metin){
//            $kes = explode($bununla,$metin);
//            $yinekes = explode($bunun,$kes[1]);
//            return $yinekes[0];
//        }
//
//
//        $em = $this->getDoctrine()->getManager();
//        $kartKapama = $em->getRepository(IvrServiceLog::class)->createQueryBuilder("ivr")
//            ->select("ivr.callId,ivr.response")
//            ->where("ivr.alias=:alias")
//            ->setParameter("alias", "İbb Menü Kart Sorgulama")
//            ->getQuery()->getArrayResult();
//
//        $arr = [];
//        foreach ($kartKapama as $log) {
//            $ivrDial = $em->getRepository(IvrLogs::class)->createQueryBuilder("dial")
//                ->select("dial.choice")
//                ->where("dial.callId=:callId")
//                ->setParameter("callId", $log["callId"])
//                ->andWhere("dial.ivrId=:ivrId")
//                ->setParameter("ivrId", 23)
//                ->getQuery()->getSingleScalarResult();
//            $doc = new \DOMDocument();
//            $doc->loadXML($log["response"]);
//            $i = 0;
//            foreach ($doc->getElementsByTagNameNS('http://schemas.xmlsoap.org/soap/envelope/', '*') as $elements) {
//                $arr [] = [
//                    $ivrDial,
//                    $elements->getElementsByTagName("MifareId")->item($i)->nodeValue,
//                    $elements->getElementsByTagName("ProductName")->item($i)->nodeValue,
//                    $elements->getElementsByTagName("CardPressDate")->item($i)->nodeValue
//                ];
//                $i++;
//            }
//        }
//
//        return $this->json($arr);
//    }

//    }