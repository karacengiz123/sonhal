<?php

namespace App\Controller;

use App\Asterisk\Entity\Cdr;
use App\Asterisk\Entity\PsContacts;
use App\Asterisk\Entity\QueueLog;
use App\Entity\AcwLog;
use App\Entity\AcwType;
use App\Entity\AgentBreak;
use App\Entity\BreakType;
use App\Entity\Calls;
use App\Entity\HoldLog;
use App\Entity\RegisterLog;
use App\Entity\User;
use App\Entity\UserProfile;
use App\Repository\UserRepository;
use koolreport\widgets\google\AreaChart;
use koolreport\widgets\google\PieChart;
use r\Queries\Dates\Time;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class HomePageController extends AbstractController
{
    /**
     * @Route("/", name="home_page")
     * @param UserInterface $user
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function index(UserInterface $user)
    {

        $newdate = new \DateTime();
//        $acwType=null;
        $sDate = $newdate->format("Y-m-d 00:00:00");
        $eDate = $newdate->format("Y-m-d 23:59:59");


        // Entity manager ile Repository tanımlamaları
        $em = $this->getDoctrine()->getManager();
        $asteriskEm = $this->getDoctrine()->getManager('asterisk');
        $agentBreakRepository = $em->getRepository(AgentBreak::class);
        $acwTypesRepository = $em->getRepository(AcwType::class);
        $acwLogsRepository = $em->getRepository(AcwLog::class);
        $queueLogRepository = $asteriskEm->getRepository(QueueLog::class);
        $userProfileRepository = $em->getRepository(UserProfile::class);
        $callsRepository = $em->getRepository(Calls::class);
        $userRepository = $em->getRepository(User::class);


        /////Başarılı ÇAĞRI SAYISI

        $completedCalls = $callsRepository->createQueryBuilder("cl")
            ->select("count(cl.idx)")
            ->where("cl.dtExten is not null")
            ->andWhere("cl.callStatus=:status")
            ->andWhere("cl.dt between :sDate and :eDate")
            ->andWhere("cl.callType=:ctype")
            ->setParameters(["status" => "Done", "sDate" => $sDate, "eDate" => $eDate, "ctype" => "Inbound"])
            ->getQuery()->getSingleScalarResult();

        /////GELEN ÇAĞRI SAYISI
        ///
        $totalCalls = $callsRepository->createQueryBuilder("cl")
            ->select("count(cl.idx)")
            ->where("cl.dt between :sDate and :eDate")
            ->andWhere("cl.callType=:ctype")
            ->setParameters(["sDate" => $sDate, "eDate" => $eDate, "ctype" => "Inbound"])
            ->getQuery()->getSingleScalarResult();


        /////Kaçan ÇAĞRI SAYISI
        ///
        $missedCalls = $callsRepository->createQueryBuilder("cl")
            ->select("count(cl.idx)")
            ->where("cl.dtQueue is not null")
            ->andWhere("cl.dtExten is null")
            ->andWhere("cl.callStatus=:status")
            ->andWhere("cl.dt between :sDate and :eDate")
            ->andWhere("cl.callType=:ctype")
            ->setParameters(["status" => "Done", "sDate" => $sDate, "eDate" => $eDate, "ctype" => "Inbound"])
            ->getQuery()->getSingleScalarResult();

        // agent güncel mola bilgisi

//        $agentBreaks = $agentBreakRepository->createQueryBuilder('ab')
//            ->select('count(ab.id)')
//            ->where('ab.endTime IS NULL')
//            ->andWhere('ab.startTime >:start')
//            ->setParameter('start', $sDate)
//            ->getQuery()
//            ->getSingleScalarResult();


        ///// HATTAKI AGENT SAYISI  inboundta konuşan agent sayısı
        ///

        $inboundCaller = $userRepository->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.state=:statu')
            ->setParameters(["statu" => "8"])->getQuery()->getSingleScalarResult();

// $callsRepository->createQueryBuilder("cl")
//            ->select("count(cl.idx)")
//            ->where("cl.callType=:ctype")
//
//            ->andWhere("cl.dt between :sDate and :eDate")
//            ->andWhere("cl.callStatus=:cStatus")
//            ->andWhere("cl.dtExten IS NOT NULL")
//            ->setParameters(["sDate" => $sDate, "eDate" => $eDate, "ctype" => "Inbound", "cStatus" => "Active"])
//            ->getQuery()->getSingleScalarResult();


//////toplam mola süreleri

        /**
         * @todo "BÜTÜN MOLA SÜRELERİNİN TOPLAMI"
         */
        $agentBreakTimes = $this->getDoctrine()->getRepository(AgentBreak::class)->createQueryBuilder("ab")
            ->select("SUM(ab.duration)")
            ->where("ab.user=:user")
            ->andWhere("ab.endTime IS NOT NULL")
            ->andWhere("ab.startTime BETWEEN :startDate AND :endDate")
            ->setParameters([
                "user" => $user,
                "startDate" => $sDate,
                "endDate" => $eDate
            ])
            ->getQuery()->getSingleScalarResult();

        $totalBreak = 0;
        if ($agentBreakTimes > 0) $totalBreak = $agentBreakTimes;


        $totalBreakResult = gmdate("H:i:s", $totalBreak);


        $agentAcwTimes = $this->getDoctrine()->getRepository(AcwLog::class)->createQueryBuilder("al")
            ->select("SUM(al.duration)")
            ->where("al.user=:user")
            ->andWhere('al.acwType=:acwType')
            ->andWhere("al.endTime IS NOT NULL")
            ->andWhere("al.startTime BETWEEN :startDate AND :endDate")
            ->setParameters([
                "user" => $user,
                "acwType" => 1,
                "startDate" => $sDate,
                "endDate" => $eDate
            ])
            ->getQuery()->getSingleScalarResult();
//
        $totalAcw = 0;
        if ($agentAcwTimes > 0) $totalAcw = $agentAcwTimes;

        $totalAcwResult = gmdate("H:i:s", $totalAcw);


        // $exten=$user->getUserProfile()->getExtension();
        $callTimes = $this->getDoctrine()->getRepository(Calls::class)->createQueryBuilder("c")
            ->select("SUM(c.durExten)")
            ->where("c.user=:user")
            ->andWhere("c.dtExten IS NOT NULL")
            ->andWhere("c.dtHangup IS NOT NULL")
            ->andWhere("c.dtExten BETWEEN :startDate AND :endDate")
            ->andWhere("c.callType=:ctype")
            ->setParameters([
                "user" => $user,
                "startDate" => $sDate,
                "endDate" => $eDate,
                "ctype" => "Inbound"
            ])
            ->getQuery()->getSingleScalarResult();

        $totaldiffCallTime = 0;
        if ($callTimes > 0)
            $totaldiffCallTime = $callTimes;


        $totalCallTimeResult = gmdate("H:i:s", $totaldiffCallTime);


        /////GELEN ÇAĞRI SAYISI Usera Göre
        ///
        $totalCallsPersonals = $callsRepository->createQueryBuilder("cl")
            ->select("count(cl.idx)")
            ->where("cl.user=:user")
            ->andWhere("cl.dt between :sDate and :eDate")
            ->andWhere("cl.callType=:ctype")
            ->setParameters([
                "user" => $user,
                "sDate" => $sDate,
                "eDate" => $eDate,
                "ctype" => "Inbound"
            ])
            ->getQuery()->getSingleScalarResult();


        $em = $this->getDoctrine()->getManager();
        $agent = $em->getRepository(AgentBreak::class);

//Moladaki Agent Sayısı

        $agentBreaks = $userRepository->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.state=:statu')
            ->setParameters(["statu" => "4"])->getQuery()->getSingleScalarResult();

//        $agentBreak = $agent->createQueryBuilder('ab')
//            ->select('count(ab.id)')
//            ->where('ab.endTime =:endTime')
//            ->setParameter('endTime', null)
//            ->getQuery()->getSingleScalarResult();
//dump($agentBreak);
//exit();
        $asteriskEm = $this->getDoctrine()->getManager('asterisk');
        $quelog = $asteriskEm->getRepository(Cdr::class);
        $asteriskquelog = $asteriskEm->getRepository(QueueLog::class);


        $answerCall = $asteriskquelog->createQueryBuilder('ac')
            ->select('count(ac.id)')
            ->where('ac.event =:event')
            ->setParameter('event', 'CONNECT')
            ->andWhere('ac.created =:created')
            ->setParameter('created', '%' . date("Y-m-d") . '%')
            ->getQuery()->getSingleScalarResult();

//Günlük Başarılı arama
        $weekanswerCall = array();
        $weekDays = array();

        for ($i = 6; $i > -1; $i--) {
            $answerCall = $quelog->createQueryBuilder('q')
                ->select('count(q.cdrId)')
                ->where('q.calldate LIKE :calldate')
                ->setParameter('calldate', '%' . date("Y-m-d", strtotime(date("Y-m-d", strtotime("-" . $i . " days")))) . '%')
                ->getQuery()->getSingleScalarResult();

            $weekanswerCall[] = $answerCall;
            $weekDays[] = date("d", strtotime(date("d-m-Y", strtotime("-" . $i . " days"))));
        }
//Toplam Arama
        $cdrTotalCalls = $quelog->createQueryBuilder('q')
            ->select('count(q.cdrId)')
            ->where('q.calldate LIKE :calldate')
            ->setParameter('calldate', '%' . date("Y-m-d") . '%')
            ->getQuery()->getSingleScalarResult();
//Kaçan Çağrı
        $weekMissed = array();
        for ($i = 6; $i > -1; $i--) {
            $weekMissedCalls = $quelog->createQueryBuilder('q')
                ->select('count(q.cdrId)')
                ->where('q.duration=:duration')
                ->setParameter('duration', '0')
                ->andWhere('q.calldate LIKE :calldate')
                ->setParameter('calldate', '%' . date("Y-m-d", strtotime(date("Y-m-d", strtotime("-" . $i . " days")))) . '%')
                ->getQuery()->getSingleScalarResult();

            $weekMissed[] = $weekMissedCalls;
        }
        $CallsArray = array(
            array("day" => "$weekDays[0]", "Başarılı" => $weekanswerCall[0], "Kaçan" => $weekMissed[0]),
            array("day" => "$weekDays[1]", "Başarılı" => $weekanswerCall[1], "Kaçan" => $weekMissed[1]),
            array("day" => "$weekDays[2]", "Başarılı" => $weekanswerCall[2], "Kaçan" => $weekMissed[2]),
            array("day" => "$weekDays[3]", "Başarılı" => $weekanswerCall[3], "Kaçan" => $weekMissed[3]),
            array("day" => "$weekDays[4]", "Başarılı" => $weekanswerCall[4], "Kaçan" => $weekMissed[4]),
            array("day" => "$weekDays[5]", "Başarılı" => $weekanswerCall[5], "Kaçan" => $weekMissed[5]),
            array("day" => "$weekDays[6]", "Başarılı" => $weekanswerCall[6], "Kaçan" => $weekMissed[6])

        );
        $answerCallsArray = array(
            array("category" => "Cevaplanan", "Piece" => 32, "profit" => 12000),
            array("category" => "Kaçan", "Piece" => 30, "profit" => 7000),

        );

        $dashboard = AreaChart::create(array(
            "title" => "Başarılı vs Kaçan",
            "dataSource" => $CallsArray,
            "columns" => array(
                "day",
                "Başarılı" => array(
                    "label" => "Başarılı",
                    "type" => "number"
                ),
                "Kaçan" => array(
                    "label" => "Kaçan",
                    "type" => "number"
                ),
            ),), true);

        $answerCallsChart = PieChart::create(array(
            "height" => "300px",
            "dataSource" => $answerCallsArray,
            "columns" => array(
                "category",
                "Piece" => array(
                    "type" => "number",

                )
            )
        ), true);


        $questions = $this->getDoctrine()->getRepository(AcwLog::class)->createQueryBuilder("al")
            ->select("SUM(al.duration)")
            ->where("al.user=:user")
            ->andWhere('al.acwType=:acwType')
            ->andWhere("al.endTime IS NOT NULL")
            ->andWhere("al.startTime BETWEEN :startDate AND :endDate")
            ->setParameters([
                "user" => $user,
                "acwType" => 2,
                "startDate" => $sDate,
                "endDate" => $eDate
            ])
            ->getQuery()->getSingleScalarResult();
//
        $totalquestions = 0;
        if ($questions > 0) $totalquestions = $questions;

        $totalquestions = gmdate("H:i:s", $questions);


        if ($this->isGranted("ROLE_TAKIM_LIDREEE")) {
            $breakTypesList = $this->getDoctrine()->getManager()->getRepository(BreakType::class)->findAll();
            $acwTypesList = $this->getDoctrine()->getManager()->getRepository(AcwType::class);

            $acwTypesList = $acwTypesList->createQueryBuilder("at");
            $acwTypesList
                ->where(
                    $acwTypesList->expr()->notIn("at.id", [3])
                )
                ->andWhere(
                    $acwTypesList->expr()->in("at.role", ["ROLE_AGENT"])
                );
            $acwTypesList = $acwTypesList->getQuery()->getResult();
        }


        $returnData = [];
        $returnData ["controller_name"] = 'HomePageController';
        $returnData ["dashboard"] = $dashboard;
        $returnData ["completedCalls"] = $completedCalls;
        $returnData ["totalCalls"] = $totalCalls;
        $returnData ["missedCalls"] = $missedCalls;
        $returnData ["agentBreaks"] = $agentBreaks;
        $returnData ["inboundCaller"] = $inboundCaller;
        $returnData ["totalBreakResult"] = $totalBreakResult;
        $returnData ["totalAcwResult"] = $totalAcwResult;
        $returnData ["totalquestions"] = $totalquestions;
        $returnData ["totalCallTimeResult"] = $totalCallTimeResult;
        $returnData ["totalCallsPersonals"] = $totalCallsPersonals;
        $returnData ["answerCallsChart"] = $answerCallsChart;

        if ($this->isGranted("ROLE_TAKIM_LIDREEE")) {
            $returnData ["breakTypesList"] = $this->toJson($breakTypesList);
            $returnData ["acwTypesList"] = $this->toJson($acwTypesList);
        }

        return $this->render('home_page/index.html.twig', $returnData);
    }

    public function toJson($data)
    {
        return $this->container->get('serializer')->serialize($data, 'json');
    }

    /**
     * @Route("/api/homepage/state", name="api_homepage_state")
     * @param UserInterface $user
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function state(UserInterface $user)
    {
        $em = $this->getDoctrine()->getManager();
        $userRepository = $em->getRepository(User::class);
        $acwLogRepository = $em->getRepository(AcwLog::class);
        $agentBreakRepository = $em->getRepository(AgentBreak::class);

        /**
         * @var User $user
         */
        $apply = $userRepository->applyOrder($user);

        $returnData = [];

        if (is_array($apply)) {

            $returnData['state'] = $apply['state'];
            $returnData['text'] = $apply['name'];
            $returnData['timeStamp'] = 0;

            $userRepository->setHomeState($user, $apply['state']);
            $userRepository->setChatState($user, 3);

        } elseif (in_array($user->getState(), [5, 11])) {

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

            $userRepository->setHomeState($user, 1);

            $state = $user->getState();
            $text = "Hazır";
            $timeStamp = $user->getLastStateChange()->getTimestamp();

        }

        $returnData['state'] = $state;
        $returnData['text'] = $text;
        $returnData['timeStamp'] = $timeStamp;


        return $this->json($returnData);
    }

    /**
     * @Route("/api/homepage/acwLogStart/{acwType}", name="api_homepage_acwlogstart")
     * @param UserInterface $user
     * @param UserRepository $userRepository
     * @param AcwType $acwType
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function acwLogStart(UserInterface $user, UserRepository $userRepository, AcwType $acwType)
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
            $acwLog = new AcwLog();
            $acwLog
                ->setUser($user)
                ->setDuration(0)
                ->setStartTime(new \DateTime())
                ->setAcwType($acwType);
            $em->persist($acwLog);
            $em->flush();

            $text = $acwType->getName();
            $state = $acwType->getState();
        }

        $userRepository->setHomeState($user, $state);
        $userRepository->setChatState($user, 3);

        return new JsonResponse(["state" => $state, "text" => $text]);
    }

    /**
     * @Route("/api/homepage/acwLogStop", name="api_homepage_acwlogstop")
     * @param UserInterface $user
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function acwLogStop(UserInterface $user, UserRepository $userRepository)
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
            $state = 1;
            $text = "Hazır";
        }
        $userRepository->setHomeState($user, $state);
        $userRepository->setChatState($user, 3);

        return new JsonResponse(["state" => $state, "text" => $text]);
    }

    /**
     * @Route("/api/homepage/breakStart/{breakType}", name="api_homepage_breakStart")
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
        $userRepository->setHomeState($user, $state);
        $userRepository->setChatState($user, 3);
        return new JsonResponse(["state" => $state, "text" => $text]);
    }

    /**
     * @Route("/api/homepage/breakStop", name="api_homepage_breakstop")
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
        $userRepository->setHomeState($user, $state);
        $userRepository->setChatState($user, 3);

        return new JsonResponse(["state" => $state, "text" => $text]);
    }

}
