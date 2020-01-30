<?php
/**
 * Created by PhpStorm.
 * User: aydn33
 * Date: 6.02.2019
 * Time: 16:43
 */

namespace App\Controller;


use App\Asterisk\Entity\QueueLog;
use App\Asterisk\Entity\Queues;
use App\Entity\AcwLog;
use App\Entity\AcwType;
use App\Entity\AgentBreak;
use App\Entity\BreakType;
use App\Entity\Calls;
use App\Entity\Chat;
use App\Entity\Evaluation;
use App\Entity\Group;
use App\Entity\HoldLog;
use App\Entity\LogEvaluation;
use App\Entity\LoginLog;
use App\Entity\RegisterLog;
use App\Entity\Role;
use App\Entity\Team;
use App\Entity\User;
use App\Form\RoleGroupType;
use App\Helpers\Date;
use App\Rethink\Service;
use phpDocumentor\Reflection\Types\Null_;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use r;
use Symfony\Component\Security\Core\User\UserInterface;

class TestTwoController extends AbstractController
{


    /**
     * @Route("/hebebbb")
     * @param UserInterface $user
     * @throws \Exception
     */
    public function deneme(UserInterface $user)
    {
        //denemeeeeeeeeeeeeeeeeee
        $em = $this->getDoctrine()->getManager();
        $breakTypesRepository = $em->getRepository(BreakType::class);
        $agentBreakRepository = $em->getRepository(AgentBreak::class);
        $acwTypesRepository = $em->getRepository(AcwType::class);
        $acwLogsRepository = $em->getRepository(AcwLog::class);
        $callsRepository = $em->getRepository(Calls::class);
        $holdLogRepository = $em->getRepository(HoldLog::class);
        $registerLogRepository = $em->getRepository(RegisterLog::class);

        $acwTypes = $acwTypesRepository->findAll();
        $breakTypes = $breakTypesRepository->findAll();


//        $registerLog = $registerLogRepository->findOneBy(["user" => $user]);
        $registerLog = $registerLogRepository->findAll();
        if (!is_null($registerLog)) {

            foreach ($registerLog as $regg) {
//                if ($regg->getDuration() == 0) {
                if ($regg->getDuration() == 0) {
                        $duration = $regg->getLastRegister()->getTimestamp() - $regg->getStartTime()->getTimestamp();
                        $regg
                            ->setDuration($duration);
                        $em->persist($regg);
                        $em->flush();
                    }
                }
            }
//        }


        $loginLogs = $em->getRepository(LoginLog::class)->createQueryBuilder("ll")
            ->where("ll.userId=:user")
            ->andWhere("ll.StartTime BETWEEN :startDate AND :endDate")
            ->setParameters([
                "user" => 577,
                "startDate" => "2019-10-15 10:12:11.0",
                "endDate" => "2019-10-15 23:59:59.0",
            ])->getQuery()->getResult();


        $loginTime = 0;
        foreach ($loginLogs as $loginLog) {
            if ($loginLog->getEndTime() == null) {
                $loginTime += Date::diffDateTimeToSecond(new \DateTime(), $loginLog->getStartTime());
            } else {
                $loginTime += Date::diffDateTimeToSecond($loginLog->getEndTime(), $loginLog->getStartTime());
            }

            $row ["loginTime"] = gmdate("H:i:s", $loginTime);

            dump($row ["loginTime"]);
            exit();
        }


        $callsEnterque = $callsRepository->createQueryBuilder('c')
//            ->select("c as callItem")
            ->Where('c.dt BETWEEN :startDate AND :endDate')
            ->andWhere('c.exten=:exten')
            ->andWhere('c.callStatus=:callStatus')
            ->andWhere("c.callType=:ctype")
            ->setParameters([
                "startDate" => "2019-10-10 00:00:00",
                "endDate" => "2019-10-10 23:59:59",
                "exten" => 9341001243,
                "callStatus" => "Done",
                "ctype" => "Inbound"
            ])
            ->getQuery()->getResult();

        $holdInboundSum = 0;
        foreach ($callsEnterque as $forCallInnboundHold) {
            /**
             * @var  HoldLog $inboundHolds
             */
            $inboundHolds = $holdLogRepository->findBy(["uniqueId" => $forCallInnboundHold->getUid2()]);

            foreach ($inboundHolds as $inboundHold) {
                $holdInboundSum += $inboundHold->getDuration();

            }

        }

        dump($holdInboundSum);
        exit();


        $users = $em->getRepository(User::class)->findAll();
//            $users = $team->getUsers()->toArray();
        $userId = [];
        /**
         * @var User $user
         */
        foreach ($users as $user) {
            $userId [] = $user->getId();
        }

        $evaluations = $this->getDoctrine()->getRepository(Evaluation::class)->findAll();
        $logEvoRes = [];
        foreach ($evaluations as $evaluation) {
            $logEvos = $em->getRepository(LogEvaluation::class)->findBy(["objectId" => $evaluation->getId()]);
//            $logEvos = $em->getRepository(LogEvaluation::class)->findBy(["objectId" => 815]);

            $varItiraz = false;
            $varObjectId = 0;
            $countProtest = 0;
            $countOKProtest = 0;
            foreach ($logEvos as $logEvo) {
                if (isset($logEvo->getData()["status"])) {
                    if ($logEvo->getData()["status"]["id"] == 3) {
                        $countProtest += 1;

                        $strCreatedDate = strtotime(date_format($evaluation->getCreatedAt(), "Y-m-d H:i:s"));
                        $strUpdatedDate = strtotime(date_format($evaluation->getUpdatedAt(), "Y-m-d H:i:s"));
                        $strDiff = $strUpdatedDate - $strCreatedDate;
                        $arrDiffMax[] = $strDiff;

                        $twoDays = 0;
                        $arrMax = max($arrDiffMax);
                        $maxDate = date("d H:i:s", $arrMax);
                        foreach ($arrDiffMax as $item) {
                            if ($item > 172800) {
                                $twoDays += 1;
                            }
                        }
                        $logEvoRes [] = [
                            "evaID" => $logEvo->getObjectId(),
                            "status" => $logEvo->getData()["status"]["id"],
                            "createdDate" => date_format($evaluation->getCreatedAt(), "Y-m-d H:i:s"),
                            "updatedDate" => date_format($evaluation->getUpdatedAt(), "Y-m-d H:i:s"),
                            "twoDays" => $twoDays,
                            "maxTime" => $arrMax,
                            "maxTimetoDate" => $maxDate,
                            "countProtest" => $countProtest,
                            "countOKProtest" => $countOKProtest,

                        ];


                        $varObjectId = $logEvo->getObjectId();
                        $varItiraz = true;
                    } else {
                        if ($varObjectId == $logEvo->getObjectId() && $varItiraz == true) {
                            if ($logEvo->getData()["status"]["id"] == 5) {
                                $countOKProtest += 1;
                                $strCreatedDate = strtotime(date_format($evaluation->getCreatedAt(), "Y-m-d H:i:s"));
                                $strUpdatedDate = strtotime(date_format($evaluation->getUpdatedAt(), "Y-m-d H:i:s"));
                                $strDiff = $strUpdatedDate - $strCreatedDate;
                                $arrDiffMax[] = $strDiff;

                                $twoDays = 0;
                                $arrMax = max($arrDiffMax);
                                $maxDate = date("d H:i:s", $arrMax);
                                foreach ($arrDiffMax as $item) {
                                    if ($item > 172800) {
                                        $twoDays += 1;
                                    }
                                }
                                $logEvoRes [] = [
                                    "evaID" => $logEvo->getObjectId(),
                                    "status" => $logEvo->getData()["status"]["id"],
                                    "createdDate" => date_format($evaluation->getCreatedAt(), "Y-m-d H:i:s"),
                                    "updatedDate" => date_format($evaluation->getUpdatedAt(), "Y-m-d H:i:s"),
                                    "twoDays" => $twoDays,
                                    "maxTime" => $arrMax,
                                    "maxTimetoDate" => $maxDate,
                                    "countProtest" => $countProtest,
                                    "countOKProtest" => $countOKProtest,
                                    "countRedProtest" => $countProtest - $countOKProtest,

                                ];


                                $varObjectId = 0;
                                $varItiraz = false;
                            }
                        } else {
                            $varObjectId = 0;
                            $varItiraz = false;
                        }
                    }

                }
            }
        }
        dump($logEvoRes);
        exit();


    }


    /**
     * @Route("/history/{evaluation}", name="quality_history")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */

    public function history(Request $request, Evaluation $evaluation)
    {
        $fieldNames = [
            'id' => 'id',
            'score' => 'Skor',
            'status' => 'Durum',
            'evaluative_comment' => 'Değerlendirilen Yorumu',
            'evaluator_comment' => 'Değerlendiren Yorumu',
            'reset_reason_id' => 'Sıfırlama Nedeni',
            'duration' => 'Süre',
            'comment' => 'Yorum',

        ];
        $logRepo = $this->getDoctrine()->getRepository(LogEvaluation::class);

        $logs = array_reverse($logRepo->getLogEntries($evaluation));

        foreach ($logs as $index => $log) {
//
            $oldData = $log->getData();

            if ($index > 0) {
                $datas = $log->getData();


                foreach ($datas as $field => $data) {

                    $changed['field'] = $field;
                    $changed['fieldHumanRead'] = [$field];
                    if (is_object($data)) {
                        if (isset($data->date)) {
                            $changed["data"] = $data->format("d-m-Y H:i:s");
                        }
                    } elseif (is_array($data)) {
                        if (isset($data["id"])) {
                            $changed["data"] = $data["id"];
                        }
                    } else {
                        $changed['data'] = $data;
                    }
                    $changed['username'] = $log->getUsername();
                    $changed['loggedAt'] = $log->getLoggedAt()->format('d-m-Y H:i:s');
                    $history[] = $changed;
                }

            }

        }
        foreach ($history as $agentEvaluationHistory) {
            $agentField = $agentEvaluationHistory["field"];
        }

        return new JsonResponse($history);
    }


    /**
     * @Route("/denemeumur")
     */
    public function asdas()
    {
        $em = $this->getDoctrine()->getManager();


        $groupRepo = $em->getRepository(Group::class);

        $sss = $groupRepo->find(1);


        $aaa = $em->find(User::class, 1);

        dump($aaa->getGroups()->contains($sss));
        dump($sss);
        exit();


        $chatTypesRepository = $em->getRepository(Chat::class);


        $totalChatCallsPersonals = $chatTypesRepository->createQueryBuilder("cl")
//            ->where("cl.user=:user")
//            ->andWhere("cl.dt between :sDate and :eDate")
            ->setParameters([
//                "user" => $userId,
//                "sDate" => $sDate,
//                "eDate" => $eDate,
            ])
            ->getQuery()->getResult();
        foreach ($totalChatCallsPersonals as $totalChatCallsPersonal) {
            dump($totalChatCallsPersonal);
            exit();

        }


        $teams = $em->getRepository(Team::class)->findAll();
        foreach ($teams as $team) {

            $choiceTeam [$team->getName()] = $team->getId();

        }


        $team = $em->find(Team::class, $choiceTeam[$team->getName()]);
        $users = $team->getUsers()->toArray();

        $userId = [];
        /**
         * @var User $user
         */
        foreach ($users as $user) {
            $userId [] = $user->getId();

        }

        $evaluations = $this->getDoctrine()->getRepository(Evaluation::class);

        $evaluatedLists = $evaluations->createQueryBuilder('e')
//            ->where("e.evaluative=:evaluative")
            ->andWhere("e.status=:status")
//            ->setParameter("evaluative",$userId)
            ->setParameter("status", 3)
            ->getQuery()->getResult();
        dump($evaluatedLists);
        exit();

    }


    /**
     * @Route("/test-aydin", name="test_aydin")
     */
    public function testAydin(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $group = new Group("");


        $form = $this->createForm(RoleGroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $formHandle = $form->getViewData();
            $selectGroup = $em->find(Group::class, $formHandle->getName()->getId());
            $selectRoles = $formHandle->getRoles();
            $roles = $em->getRepository(Role::class);
            $roles = $roles->createQueryBuilder("r");
            $roles
                ->select("r.role")
                ->where($roles->expr()->in("r.title", $selectRoles)
                );
            $roles = $roles->getQuery()->getArrayResult();
            $selectRolesResult = [];
            foreach ($roles as $role) {
                $selectRolesResult [] = $role["role"];
            }
            $selectGroup->setRoles($selectRolesResult);
            $em->persist($selectGroup);
            $em->flush();

            return $this->redirectToRoute("test_aydin");
        }

        return $this->render("test/test_aydin.hmtl.twig", [
            "form" => $form->createView(),
        ]);
    }


    /**
     * @Route("/test-aydin-two")
     */
    public function testAydinTwo()
    {
        throw new \Exception("asdasd");
        return new Response("OK");
    }


    /**
     * @Route("/TestTwig")
     */
    public function testtt()
    {

        throw new \Exception('My first Sentry error!');

        return $this->render("test/test.html.twig");
    }

    /**
     * @Route("/ccPulse/rethink-db-all-select", name="r_queue_select" )
     * @param Service $rethink
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function queueSelection(Request $request)
    {
//        $column['allEnterqueue'] = r\table('queues')->pluck(['idx','description'])->coerceTo('array')->run($rethink->connect());
//
//        return $this->json($column['allEnterqueue']);
        $q = $request->get('q');
        $em = $this->getDoctrine()->getManager();
        $queueRepo = $em->getRepository(Queues::class);
        $queues = $queueRepo->createQueryBuilder('qu')
            ->select('qu.queue,qu.description')
            ->getQuery()->getArrayResult();

        return $this->json($queues);


    }


    /**
     * @Route("/ccPulse/r-quue-get-select-all", name="queue_get_select_all")
     * @param Service $rethink
     * @param Request $request
     * @return mixed
     */

    public function testCssssssss(Service $rethink, Request $request)
    {


//        $a = r\table('queue_log')->filter(array("event" => "CONNECT","queuename"=>"934100102"))->pluck(["data1"])->coerceTo('array')->run($rethink->getConnection());
//        $d = r\table('queue_log')->filter(array("event" => "CONNECT","queuename"=>"934100102"))->pluck(["data1"])->max()->run($rethink->getConnection());
//        $e = r\table('queue_log')->filter(array("event" => "CONNECT","queuename"=>"934100102"))->pluck(["data1"])->coerceTo('array')->run($rethink->getConnection());
//
//        $c = 0;
//        foreach ($a as $b){
//            if ($c < $b['data1']+0){
//                $c = $b['data1']+0;
//            }
//        }
//
//
//
//        dump($c);
//        dump($d);
//        dump($e);
//        exit();
//        $em = $this->getDoctrine()->getManager();
//        $queueRepo = $em->getRepository(Queues::class);
//        $queues = $queueRepo->createQueryBuilder('qu')
//            ->select('qu.queue,qu.description')
//            ->getQuery()->getArrayResult();
//
//        foreach ($queues as $queue) {
//            dump($queue["description"]);
//        }
//
//        dump($queues);exit();
        $row = array();
        $column = array();

        $filter = array();
//        $column['serviceLevel']=r\table('queue_log')
//            ->filter($filter)->pluck(["data3"])->run ($rethink->getConnection());

//        dump($column['serviceLevel']);
//        exit();
        if ($request->get('queueName') == "allQueue") {//indexte selectteki value
            $em = $this->getDoctrine()->getManager();
            $queueRepo = $em->getRepository(Queues::class);
            $queues = $queueRepo->createQueryBuilder('qu')
                ->select('qu.queue,qu.description')
                ->getQuery()->getArrayResult();

            foreach ($queues as $queue) {
                $filter['queuename'] = $queue["queue"];
                $column['description'] = $queue["description"];

                //*************** ENTERQUEUE 205 adet***************
                $filter['event'] = "ENTERQUEUE";
                $column['enterqueue'] = r\table('queue_log')
                    ->filter($filter)
                    ->count()->run($rethink->getConnection());

                //*************** Connect***************
                $filter['event'] = "CONNECT";
                $column['connect'] = r\table('queue_log')
                    ->filter($filter)
                    ->count()->run($rethink->getConnection());
                //        $column['connectt'] = $column['enterqueue'] - $column['ABANDON'];


                //*************** KACAN CAGRI***************
                $filter['event'] = "ABANDON";
                $column['ABANDON'] = r\table('queue_log')
                    ->filter($filter)
                    ->count()->run($rethink->getConnection());

                //*************** AVG ortalama***************
                $filter['event'] = "ABANDON";
                $avgAbandons = r\table('queue_log')
                    ->filter($filter)
                    ->pluck(["data3"])->coerceTo('array')
                    ->run($rethink->getConnection());
                $data3_array = [];
                foreach ($avgAbandons as $data3) {
                    $data3_array [] = $data3['data3'] + 0;//strıngı ınteger yapıyor
                }
                $x = count($data3_array) ? array_sum($data3_array) / count($data3_array) : 0;
                $column['AvgABANDON'] = round($x);


                //*************** BEKLEYEN********************
                $filter['event'] = "CONNECT";
                $column['waitConnect'] = r\table('queue_log')
                    ->filter($filter)
                    ->pluck(["data1"])->count()
                    ->run($rethink->getConnection());

                //*************** bekleeyen max r***************
//                $filter['event'] = "CONNECT";
//                $column['waitConnectMax'] = r\table('queue_log')
//                    ->filter($filter)
//                    ->pluck(["data1"])->max()
//                    ->run($rethink->getConnection());

                //*************** Bekletilen Max Süre***************
                //        $column['waitConnectMax2'] = r\table('queue_log')
                //            ->filter(array("event" => "CONNECT"))
                //            ->pluck(["data3"])->max()
                //            ->run($rethink->getConnection());

                //*************** cevaplanma oranı***************
                $y = ($column['enterqueue'] ? $column['connect'] / $column['enterqueue'] : 0) * 100;
                $column['ConnectRate'] = "% " . round($y);


                $row[] = $column;
            }

        } else {

            $filter['queuename'] = "" . $request->get('queueName') . "";     // tekli sorguda ısım degısıyor ama sonuclar aynı
            $column['description'] = "" . $request->get('description') . ""; // tekli sorgular ıcın kuyruk ısımlerını alıyor

            //ENTERQUEUE 205 adet
            $filter['event'] = "ENTERQUEUE";
            $column['enterqueue'] = r\table('queue_log')
                ->filter($filter)
                ->count()->run($rethink->getConnection());


            $filter['event'] = "CONNECT";
            $column['connect'] = r\table('queue_log')
                ->filter($filter)
                ->count()->run($rethink->getConnection());
            //        $column['connectt'] = $column['enterqueue'] - $column['ABANDON'];


            //KACAN CAGRI
            $filter['event'] = "ABANDON";
            $column['ABANDON'] = r\table('queue_log')
                ->filter($filter)
                ->count()->run($rethink->getConnection());

            //AVG ortalama
            $filter['event'] = "ABANDON";
            $avgAbandons = r\table('queue_log')
                ->filter($filter)
                ->pluck(["data3"])->coerceTo('array')
                ->run($rethink->getConnection());
            $data3_array = [];
            foreach ($avgAbandons as $data3) {
                $data3_array [] = $data3['data3'] + 0;//strıngı ınteger yapıyor
            }
            $x = count($data3_array) ? array_sum($data3_array) / count($data3_array) : 0;
            $column['AvgABANDON'] = round($x);
            // BEKLEYEN
            $filter['event'] = "CONNECT";
            $column['waitConnect'] = r\table('queue_log')
                ->filter($filter)
                ->pluck(["data1"])->count()
                ->run($rethink->getConnection());

            //bekleeyen max r
            //        $column['waitConnectMax'] = r\table('queue_log')
            //            ->filter(["event" => "CONNECT", "queuename" => $request->get('queueName')])
            //            ->pluck(["data1"])->max()
            //            ->run($rethink->getConnection());

            //Bekletilen Max Süre
            //        $column['waitConnectMax2'] = r\table('queue_log')
            //            ->filter(array("event" => "CONNECT"))
            //            ->pluck(["data3"])->max()
            //            ->run($rethink->getConnection());

            //cevaplanma oranı
            $y = ($column['enterqueue'] ? $column['connect'] / $column['enterqueue'] : 0) * 100;
            $column['ConnectRate'] = "% " . round($y);


//
            $row[] = $column;

        }

        return new JsonResponse($row);


//        return $this->render('@IbbStaff/pages/ccPulse.html.twig',["rows"=>$row]);
    }


    /**
     * @Route("/inboundTest")
     */
    public function inbountTest()
    {
        $em = $this->getDoctrine()->getManager();

        $acwLogs = $em->getRepository(AcwLog::class)->createQueryBuilder("acwlog")
            ->where("acwlog.startTime BETWEEN :startDate AND :endDate")
            ->setParameter('startDate', date('Y-m-d 00:00:00'))
            ->setParameter('endDate', date('Y-m-d 23:59:59'))
            ->getQuery()->getArrayResult();
//        dump($acwLogs);
//        exit();
        $users = $em->getRepository(User::class)->findAll();

        $acwTypes = $em->getRepository(AcwType::class)->findAll();

        $breakTypes = $em->getRepository(BreakType::class)->findAll();

        $row = [];
        $acw_log = [];
        foreach ($acwLogs as $acwlog) {
            foreach ($users as $user) {
                if ($acwlog["userId"] == $user->getId()) {
                    foreach ($acwTypes as $acwType) {
                        if ($acwlog["acwTypeId"] == $acwType->getId()) {
                            foreach ($breakTypes as $breakType) {
                                if ($acwlog["userId"] == $breakType->getId()) {
                                    $acwlog["user"][] = $user->getUsername();
                                    $acwlog["acwType"][] = $acwType->getName();
                                    $acwlog["breakType"][] = $breakType->getName();
                                    dump($acwlog["breakType"][]);
                                    exit();
                                    $acwlog["startTime"][] = $acwlog["startTime"];
                                    $acwlog["endTime"][] = $acwlog["endTime"];

                                    $startTime = $acwlog["startTime"];
                                    $start = $startTime->format("d-m-Y H:i:s");
                                    $resultStartTime = strtotime($start);

                                    $endTime = $acwlog["endTime"];
                                    $end = $endTime->format("d-m-Y H:i:s");
                                    $resultEndTime = strtotime($end);

                                    $acwlog["timeSn"][] = $resultEndTime - $resultStartTime;
                                }
                            }
                        }
                    }
                }
            }
        }
        $row["acw_log"] = $acw_log;

        dump($acw_log);
        exit();

        return new JsonResponse($row);
    }
}