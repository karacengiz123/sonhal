<?php
/**
 * Created by PhpStorm.
 * User: aydn33
 * Date: 6.02.2019
 * Time: 16:43
 */

namespace App\Controller;


use App\Asterisk\Entity\Cdr;
use App\Asterisk\Entity\Queues;
use App\AsteriskAction\PJSIPShowEndpointAction;
use App\Entity\AcwLog;
use App\Entity\AcwType;
use App\Entity\AgentBreak;
use App\Entity\BreakType;
use App\Entity\Calls;
use App\Entity\Chat;
use App\Entity\ChatMessage;
use App\Entity\Evaluation;
use App\Entity\FormCategory;
use App\Entity\Group;
use App\Entity\HoldLog;
use App\Entity\LogBreakType;
use App\Entity\LoginLog;
use App\Entity\Orders;
use App\Entity\QueuesMembersDynamic;
use App\Entity\RealtimeQueueMembers;
use App\Entity\Role;
use App\Entity\Session\Sessions;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\UserProfile;
use App\Helpers\Date;
use App\Repository\CallsRepository;
use App\Services\AgentStatusService;
use App\Services\SipServerService;
use PAMI\Client\Impl\ClientImpl;
use PAMI\Message\Action\ExtensionStateAction;
use PAMI\Message\Action\ListCommandsAction;
use PAMI\Message\Action\QueueStatusAction;
use PAMI\Message\Action\StatusAction;
use PAMI\Message\Action\SIPShowRegistryAction;
use function r\expr;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use function Symfony\Component\VarDumper\Dumper\esc;

class TestController extends AbstractController
{

    public function denemece()
    {


    }











    /**
     * @Route("/test-ay")
     */
    public function testAy()
    {

        $users = $this->getDoctrine()->getRepository(User::class)->findBy(["state"=>1]);

        $activeUser = [];
        foreach ($users as $user){
            if (array_search("ROLE_INBOUND",$user->getRoles()) != false){
                $activeUser []= $user;
            }
        }

        $userr = $this->getDoctrine()->getRepository(User::class);
        $avail = $userr->createQueryBuilder("u")
            ->where("u.state =:state")
            ->leftJoin("u.groups","groups")
            ->andWhere("groups.roles LIKE :roles")
            ->setParameters([
                "state"=>1,
                "roles"=>"%ROLE_INBOUND%"
            ])
            ->getQuery()->getResult();
        $avail = count($avail);

        dump($activeUser);
        dump(count($avail));
        exit();

        $x = $this->getDoctrine()->getRepository(Chat::class)->findBy(["activityId"=>"1-20572531126"]);
        $xy = $this->getDoctrine()->getRepository(Chat::class)->createQueryBuilder("c")
            ->where("c.postBody LIKE :postBody")
            ->setParameter("postBody","%TbxPbxSystemChatCallIdNumber540ae6b0f6ac6e155062f3dd4f0b2b01%")
            ->getQuery()->getResult();

        $body = json_decode($x[0]->getPostBody(),true);

        dump($x);
        dump($body);
        dump($xy);

        $y = $this->getDoctrine()->getRepository(ChatMessage::class)->findBy(["chat"=>1439]);

        dump($y);

        exit();

        $notExtension = [];


        $notExtension []= 000;

        for ($i=900; $i<=999; $i++){
            $notExtension []= $i;
        }


        dump($notExtension);
        exit();

        $evaluations = $this->getDoctrine()->getRepository(Evaluation::class)->findAll();
        $userDest = [];
        $destID []= " aıshfoıahıfohaw3oıfhaıowhfoıawhıofhaıowfghjıopawjf9uq309ru0932qafoıh3908fyau093wyf0983yhfoıahfohaoıshofıphaw309uy23r0a9uywfoıhjoısfhoıawf bununla bunun ";
        foreach ($evaluations as $evaluation){
            // kişilerin rolelerini we id lerini sıralıyıp renklendirilecek
            $roleGroups = $evaluation->getEvaluative()->getGroups()->toArray();
            /**
             * @var Group $roleGroup
             */
            foreach ($roleGroups as $roleGroup){
                foreach ($roleGroup->getRoles() as $value){
                    $userDest [$evaluation->getEvaluative()->getId()]["role"][] = $value;
                }
            }
            $userDest [$evaluation->getEvaluative()->getId()]["userName"][] = $evaluation->getEvaluative()->getUserProfile()->__toString();
            $userDest [$evaluation->getEvaluative()->getId()]["destID"][] = $evaluation->getSourceDestID();
        }
        dump($userDest);
        exit();




        $sessionsEm = $this->getDoctrine()->getManager("session");

        $sessions = $sessionsEm->getRepository(Sessions::class);

        $sessionsAll = $sessions->findAll();

        dump($sessions);
        dump($sessionsAll);

        $session = $this->get("session");
        $sessionID = $session->getId();
        $sessionAll = $session->all();
        $sessionName = $sessionAll["_security.last_username"];

        dump($session);
        dump($sessionID);
        dump($sessionAll);
        dump($sessionName);
        exit();
    }







    private $queueName;
    private $userName;

    public function queueName()
    {
        $em = $this->getDoctrine()->getManager();
        $queues = $em->getRepository(Queues::class)->findAll();
        $queueRow = [];
        foreach ($queues as $queue) {
            $queueRow[$queue->getQueue()] = $queue->getDescription();
        }
        $this->queueName = $queueRow;
    }

    public function userName()
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository(UserProfile::class)->findAll();
        $userRow = [];
        foreach ($users as $user) {
            $userRow[$user->getExtension()] = [
                "tc" => $user->getTckn(),
                "username" => $user->getFirstName() . " " . $user->getLastName()
            ];
        }
        $this->userName = $userRow;
    }


    /**
     * @Route("/test-ay-test")
     */
    public function testAyTest()
    {
        $this->queueName();

        $em = $this->getDoctrine()->getManager();
        $callsRepository = $em->getRepository(Calls::class);
        $calls = $callsRepository->createQueryBuilder("c")
            ->select("c AS callItem, c.queue, COUNT(c.idx) AS count, SUM(c.durExten) AS speakTime")
            ->andWhere("c.callStatus=:callStatus")
            ->andWhere("c.dtExten IS NOT NULL")
            ->andWhere("c.queue IS NOT NULL")
            ->andWhere("c.callType=:callType")
            ->setParameters([
                "callStatus"=>"Done",
                "callType"=>"Inbound",
            ])
            ->groupBy("c.queue, c.user")
            ->getQuery()->getResult();

        $arr = [];
        foreach ($calls as $call){
            /**
             * @var User $user
             * @var Calls $callItem
             */
            $callItem = $call["callItem"];
            $user = $callItem->getUser();
            $holdLogs = $user->getHoldLogs()->toArray();
            $countHold = 0;
            $sumHold = 0;
            if (count($holdLogs)>0){
                /**
                 * @var HoldLog $holdLog
                 */
                foreach ($holdLogs as $holdLog){
                    if ($holdLog->getCallType() == "inBound"){
                        $countHold += 1;
                        $sumHold += $holdLog->getDuration();
                    }
                }
            }
            $acwLogs = $user->getAcwLogs()->toArray();
            $sumAcw = 0;
            $acwCount = 0;
            if (count($acwLogs)>0){
                /**
                 * @var AcwLog $acwLog
                 */
                foreach ($acwLogs as $acwLog){
                    if ($acwLog->getAcwType()->getId() == 1){
                        $acwCount += 1;
                        $sumAcw += $acwLog->getDuration();
                    }
                }
            }
            $arr []= [
                "tckn"=>$user->getUserProfile()->getTckn(),
                "userName"=>$user->getUserProfile()->__toString(),
                "queue"=>$this->queueName[$call["queue"]],
                "count"=>$call["count"],
                "speakTime"=>$call["speakTime"],
                "countHold"=>$countHold,
                "sumHoldTime"=>$sumHold,
                "acht"=>round(($call["speakTime"]+$sumHold)/$call["count"],2),
                "avgAcd"=>gmdate("H:i:s",$call["count"] * 3),
                "countAcw"=>$acwCount,
                "sumAcw"=>$sumAcw,
                "avgAcw"=>gmdate("H:i:s",round($sumAcw/$acwCount)),
            ];
        }

        dump($arr);
        exit();
    }
    
    
    



    /**
     * @Route("/testCsss/{user}")
     * @return Response
     * @param User $user
     * @throws
     */
    public function testCsss(User $user, SipServerService $sipServerService,AgentStatusService $agentStatusService)
    {

        $oldChange = null;
        $logRepo = $this->getDoctrine()->getRepository(LogBreakType::class);
        $logs = array_reverse($logRepo->getLogEntries($user));
        foreach($logs as $log) {
            $data = $log->getData();

            if(is_array($oldChange) and @$data['state'] == 8 and @$oldChange['state'] != 1)
            {
                dump($oldChange);
                dump($data);
                dump($log);
            }

            $oldChange = $data;
        }
        exit();











        $em = $this->getDoctrine()->getManager();
        $roles = $em->getRepository(Role::class)->findAll();

//        $a = "";
//        $b = 0;
//        $a .= 'a:2:{s:14:"RULE_USER_LIST";s:19:"Kullanıcı Listesi";';

//        $d = [];
        foreach ($roles as $role){
//            $d []= $role->getRole();
            $user->addRole($role->getRole());
            $em->persist($user);
            $em->flush();
//            $a .= 'i:'.$b.';s:'.strlen($role->getRole()).':"'.$role->getRole().'";';
//            $b++;
        }
//        $a .= "}";

        dump($user);
        exit();



        $c->addRole("SUPER_ADMIN");
        $em->persist($c);
        $em->flush();

        dump($c);
        exit();





















        $status = $agentStatusService->status($user)->getContent();

        dump($status);
        exit();















        $em = $this->getDoctrine()->getManager();
        $rqmRepo = $em->getRepository(RealtimeQueueMembers::class);
        $inAcw = $rqmRepo->createQueryBuilder("rqm");
        $inAcw
            ->select("COUNT(rqm.uniqueid)")
            ->leftJoin("rqm.user","u")
            ->where(
                $inAcw->expr()->in(
                    "u.state",[2,5,6,11]
                )
            )
            ->andWhere("rqm.queueName=:queueName")
            ->andWhere("rqm.paused=:paused")
            ->setParameters([
                "queueName"=>934100102,
                "paused"=>1
            ]);
        $inAcw = $inAcw->getQuery()->getSingleScalarResult();

        dump($inAcw);
        exit();


        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository(User::class);

        $rqmRepo = $em->getRepository(RealtimeQueueMembers::class);
        $inBreak = $rqmRepo->createQueryBuilder("rqm")
            ->select("COUNT(rqm.uniqueid)")
            ->leftJoin("rqm.user","u")
            ->where("u.state=:state")
            ->andWhere("rqm.queueName=:queueName")
            ->andWhere("rqm.paused=:paused")
            ->setParameters([
                "state"=>4,
                "queueName"=>934100102,
                "paused"=>1
            ])->getQuery()->getSingleScalarResult();

        dump($inBreak);
        exit();


        ////ACW LİSTESİ
        ///
        $inAcw = $realtimeQueMember->createQueryBuilder("rqm")
            ->select("count(rqm.uniqueid)")
            ->where("rqm.queueName=:que")
            ->andWhere("rqm.paused=:pause")
            ->andWhere("rqm.pauseTypeTable=:ptt")
            ->setParameters(["que" => $queue["queue"], "pause" => "1", "ptt" => "AcwLog"])
            ->getQuery()
            ->getSingleScalarResult();



        dump($inAcw);
        exit();




    $em = $this->getDoctrine()->getManager();
    $userId = 125;
    $status = "";
    $internal = "";
    $extension = "";
    $internalState = [
        0 => "GİRİŞ YAPMAMIŞ",
        1 => "HAZIR",
        8 => "ÇAĞRIDA",
        2 => "ACW",
        5 => "SORU",
        6 => "DIŞ ARAMA",
        11 => "AcwLog",
        4 => "AgentBreak"
    ];

    $user = $this->getDoctrine()->getRepository(User::class)->find($userId);
    if ($user != null){
        $state = $user->getState();
        $internal = $user->getUserProfile()->getExtension();

        $order = $em->getRepository(Orders::class)->findOneBy(["user"=>$user]);
        if (!is_null($order)){
            if ($order->getStartOrStop() == 1){
                if ($order->getType() == "AgentBreak"){
                    $status = $em->find(BreakType::class,$order->getSubType())." GİRMESİ BEKLENİYOR";
                }elseif ($order->getType() == "AcwLog"){
                    $status = $em->find(AcwLog::class,$order->getSubType())." GİRMESİ BEKLENİYOR";
                }
            }elseif ($order->getStartOrStop() == 0){
                if ($order->getType() == "AgentBreak"){
                    $status = $em->find(BreakType::class,$order->getSubType())." ÇIKMASI BEKLENİYOR";
                }elseif ($order->getType() == "AcwLog"){
                    $status = $em->find(AcwLog::class,$order->getSubType())." ÇIKMASI BEKLENİYOR";
                }
            }
        }elseif ($state == 11){
            $acwLog = $em->getRepository(AcwLog::class)->findOneBy(['user'=>$user,'endTime'=>null,'duration'=>0]);
            $status = $acwLog->getAcwType()->getName();
        }elseif ($state == 4){
            $agentBreak = $em->getRepository(AgentBreak::class)->findOneBy(['user'=>$user,'endTime'=>null,'duration'=>0]);
            $status = $agentBreak->getBreakType()->getName();
        }else{
            $status = $internalState[$state];
        }
    }else{
        $status = "TEMSİLCİ BULUNAMADI";
        $internal = "";
    }

    return new JsonResponse(["status" => $status, "internal"=>$internal]);

    exit();





















        $this->queueName();
        $this->userNAme();

        $em = $this->getDoctrine()->getManager();

        $rows = [];
        $rowColumns = [];
        $resultRowColumns = [];

        $evaluations = $em->getRepository(Evaluation::class)->findBy(["evaluative"=>1]);

        if (count($evaluations)>0){
            foreach ($evaluations as $evaluation){
                $userName = $evaluation->getUser()->getUserProfile()->getFirstName()." ".$evaluation->getUser()->getUserProfile()->getLastName();
                $formCatagories = $em->getRepository(FormCategory::class)->findBy(["formTemplate"=>$evaluation->getFormTemplate()]);
                if (count($formCatagories)>0){
                    $rows [$userName][$evaluation->getFormTemplate()->getTitle()][] = $formCatagories;
                }
            }
        }


        foreach ($rows as $rowKey=>$rowValue){
            foreach ($rowValue as $valKey=>$val){
                foreach ($val as $item){
                    foreach ($item as $aaa){
                        $rowColumns [$rowKey][$valKey][$aaa->getTitle()]["sort"][]=$aaa->getSort();
                        $rowColumns [$rowKey][$valKey][$aaa->getTitle()]["maxScore"][]=$aaa->getMaxScore();
                    }
                }
            }
        }

        foreach ($rowColumns as $rowKey=>$rowValue){
            foreach ($rowValue as $valKey=>$val){
                $totalSort = 0;
                $totalMaxScore = 0;
                foreach ($val as $itemKey=>$itemValue){

                    $resultRowColumns [$rowKey][$valKey][$itemKey]["sort"]=[
                        "count"=>count($itemValue["sort"]),
//                        "sort"=>$itemValue["sort"],
                        "sumSort"=>array_sum($itemValue["sort"])
                    ];
                    $totalSort += array_sum($itemValue["sort"]);
                    $resultRowColumns [$rowKey][$valKey][$itemKey]["maxScore"]=[
                        "count"=>count($itemValue["maxScore"]),//buradakı count bıze DEGERENDİRME SAYISINI  verıyor
//                        "sort"=>$itemValue["maxScore"],
                        "sumMaxScore"=>array_sum($itemValue["maxScore"])
                    ];
                    $totalMaxScore += array_sum($itemValue["maxScore"]);
                }
                $resultRowColumns [$rowKey][$valKey]["total"]["sort"]=[
                    "count"=>count($itemValue["sort"]),
                    "sumSort"=>$totalSort
                ];
                $resultRowColumns [$rowKey][$valKey]["total"]["maxScore"]=[
                    "count"=>count($itemValue["maxScore"]),
                    "sumMaxScore"=>$totalMaxScore
                ];
            }
        }


        dump();
        dump($rowColumns);
        dump($rows);
        exit();


















        $evalationUsers = $this->getDoctrine()->getRepository(Evaluation::class)->createQueryBuilder("eva");
        $evalationUsers
            ->select("fc.title,fc.sort,fc.maxScore")
            ->where("eva.user=:user")
            ->leftJoin("eva.formTemplate","ft")
            ->leftJoin("ft.formCategories","fc");

        $evalationUsers = $evalationUsers->getQuery()->getResult();


        dump($rows);
        exit();


























        $calls = $em->getRepository(Calls::class)->createQueryBuilder("c");
        $calls
            ->select("c.exten,c.queue,count(c.idx) as count")
            ->where("c.dt BETWEEN :start AND :end")
            ->andWhere("c.dtQueue IS NOT NULL")
            ->andWhere("c.dtExten IS NOT NULL")
            ->andWhere("c.dtHangup IS NOT NULL")
            ->andWhere("c.user IS NOT NULL")
            ->andWhere("c.callStatus=:callStatus")
            ->setParameters([
                "start" => "2019-06-20 00:00:00",
                "end" => "2019-06-20 23:59:59",
                "callStatus" => "Done"
            ])
            ->groupBy("c.queue,c.user");
        $calls = $calls->getQuery()->getArrayResult();


        $rows = [];
        $columns = [];
        $columnsArr = false;
        dump($columnsArr);
        $columsCol = [];
        foreach ($this->userNAme as $userKey => $userValue) {
            foreach ($calls as $call) {
                if ($userKey == $call["exten"]){
                    $rowColumns = [];
                    $rowColumns ["dateRange"] = " ";
                    if ($columnsArr == false) {
                        $columsCol [] = ["data" => "dateRange", "name" => "dateRange", "title" => "TARİH"];
                    }
                    $rowColumns ["dateRangeTime"] = " ";
                    if ($columnsArr == false) {
                        $columsCol [] = ["data" => "dateRangeTime", "name" => "dateRangeTime", "title" => "24/15 DAKİKALIK VEYA SASATLİK"];
                    }
                    $rowColumns ["agentTc"] = $userValue["tc"];
                    if ($columnsArr == false) {
                        $columsCol [] = ["data" => "agentTc", "name" => "agentTc", "title" => "TC"];
                    }
                    $rowColumns ["agent"] = $userValue["username"];
                    if ($columnsArr == false) {
                        $columsCol [] = ["data" => "agent", "name" => "agent", "title" => "TEMSİLCİ"];
                    }
                    foreach ($this->queueName as $queueKey => $queueVal) {
                        if ($queueKey == $call["queue"]) {
                            $rowColumns [$queueVal] = $call["count"];
                        } else {
                            $rowColumns [$queueVal] = 0;
                        }
                        if ($columnsArr == false) {
                            $columsCol [] = ["data" => $queueVal, "name" => $queueVal, "title" => $queueVal];
                        }
                    }
                    $rows [] = $rowColumns;
                    $columns []= $columsCol;
                    $columnsArr = true;
                    dump($columnsArr);
                }
            }
        }

        dump($columnsArr);
        dump($columns);
        dump($rows);
        exit();


//        $call
//            ->select('c')
//            ->where("c.queue = 934100102")
//            ->groupBy("c.queue");
//
//        $call = $call->getQuery()->getResult();
//
//        dump($call);
//        exit();

        $qmdResult =
            $queue
                ->select(
                    '(' .
                    $call
                        ->select("count(c.idx)")
                        ->where("c.queue = q.queue")
                        ->andWhere("c.dtExten IS NOT NULL")
                        ->andWhere("c.dtHangup IS NOT NULL")
                        ->andWhere("c.queue IS NOT NULL")
                        ->andWhere("c.user IS NOT NULL")
                        ->groupBy("c.queue")
                    . ')'
                )//        ->having('say > 0')
        ;

        $qmdResult = $qmdResult->getQuery()->getResult();

        dump($qmdResult);
        exit();


        $nmn = new \DateTime();
        $sss = $nmn->modify("60 sec");
        dump($sss);
        exit();


        dump($sipServerService->serverName()->getContent());
        exit();


        $em = $this->getDoctrine()->getManager();


        dump($user->getUserCall()->toArray());
        exit();


        $callsRepo = $em->getRepository(Calls::class);


        $calls = $callsRepo->findAll();

        dump($calls[0]->getDurExten());
        exit();


        $acwTypeRepo = $em->getRepository(AcwType::class);


        $calls = $callsRepo->createQueryBuilder('c');
        $acwType = $acwTypeRepo->createQueryBuilder("at");


        $calls
            ->select("DATE_DIFF('2019-05-04', '2019-05-01') AS dateDiff, c.dtHangup,c.dtExten")
            ->andWhere("c.dtExten IS NOT NULL")
            ->andWhere("c.dtHangup IS NOT NULL");
        $calls = $calls->getQuery()->getResult();


        $acwType
            ->select("UPPER(at.role)");
        $acwType = $acwType->getQuery()->getResult();


        dump($calls);
        dump($acwType);
        exit();


        $calls
            ->where(
                $calls->expr()->diff(
                    "c.dtHangup",
                    "c.dtExten"
                )
            )
            ->andWhere("c.dtExten IS NOT NULL")
            ->andWhere("c.dtHangup IS NOT NULL");
        $calls = $calls->getQuery()->getResult();


        dump($calls);
        exit();


        $em = $this->getDoctrine()->getManager();
        $callsRepo = $em->getRepository(Calls::class);

        $callsTenSec = $callsRepo->createQueryBuilder('c')
            ->Where('c.dt BETWEEN :startDate AND :endDate')
            ->andWhere('c.queue=:queue')
            ->andWhere('c.dtExten is NOT NULL')
            ->andWhere('c.callStatus=:callStatus')
            ->setParameters([
                "startDate" => "2019-06-10 00:00:00",
                "endDate" => "2019-06-10 23:59:59",
                "queue" => "934100108",
                "callStatus" => "Done",
            ])
            ->getQuery()->getResult();


        $tenSec = [];
        foreach ($callsTenSec as $callSec) {
            $diff = Date::diffDateTimeToSecond($callSec->getDtExten(), $callSec->getDtQueue());
//            if ( $diff > 10 and $diff < 20){
            $tenSec [] = $diff;
//            }
        }

        dump(
            "Adet",
            count($tenSec)
        );
        dump(
            "Max",
            max($tenSec));
        dump(
            "Min",
            min($tenSec));
        dump(
            "Toplam",
            array_sum($tenSec));
        dump(
            "Ort.",
            round(count($tenSec) ? array_sum($tenSec) / count($tenSec) : 0, 2));
        exit();


        $dateTime = new \DateTime();
        $loginLogs = $this->getDoctrine()->getRepository(LoginLog::class)->createQueryBuilder("ll")
            ->where("ll.StartTime BETWEEN :startDate AND :endDate")
            ->andWhere("ll.userId=:userId")
            ->setParameters([
                "startDate" => $dateTime->format("Y-m-d 00:00:00"),
                "endDate" => $dateTime->format("Y-m-d 23:59:59"),
                "userId" => $user
            ])->getQuery()->getResult();
        $startTimeString = "";
        foreach ($loginLogs as $loginLog) {
            if ($startTimeString == "") {
                $startDate = $loginLog->getStartTime();
                $endDate = $loginLog->getStartTime();
                $startTimeString .= "1";
            }
            $diff = $startDate->diff($loginLog->getEndTime());
            $endDate->add($diff);


            dump($startDate);
            dump($endDate);
            exit();
        }

//        $diffResult = $startTime->diff($endTime);
//        dump($diffResult);
        exit();


        exit();


        $options = array(
            'host' => '10.5.95.152',
            'scheme' => 'tcp://',
            'port' => 5038,
            'username' => 'amiuser',
            'secret' => 'amiuser',
            'connect_timeout' => 10,
            'read_timeout' => 10
        );
        $client = new ClientImpl($options);
        $client->open();

        $exten = new ExtensionStateAction('9341001703', 'defasult');
        $stat = $client->send(new StatusAction(false));

        dump($stat);
        exit;

        $em = $this->getDoctrine()->getManager();


        $logRepo = $em->getRepository(LogBreakType::class);
        $breakType = $em->find(BreakType::class, 8);

        $logs = $logRepo->getLogEntries($breakType);

        dump(array_reverse($logs));

        exit;
        $users = $em->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            dump($user->getUserProfile()->getTckn());
            dump($user->getUserProfile()->getFirstName());
            dump($user->getUserProfile()->getLastName());
            exit();
        }

        exit();

//        $aaa = $this->getDoctrine()->getRepository(Cdr::class)->findAll();
//        $date = $aaa[0]->getCalldate();
//        $date = date_format($date,"Y/m/d H:i:s");
//        dump($date);
//        exit();


        $client = new \GuzzleHttp\Client();
        $usersApi = $client->request('GET', 'https://tbxsipdev.ibb.gov.tr/api/queue_summary.txt');
        $usersApi = $usersApi->getBody()->getContents();

        $usersApi = json_decode($usersApi, true);

        dump($usersApi);
        exit();


        return $this->render("test/test.html.twig");

//        $em = $this->getDoctrine()->getManager();
//        $repo = $em->getRepository(LoginLog::class)->findBy(["userId"=>$user,"EndTime"=>null]);
//
//        if (count($repo) == 1){
//            $repo[0]
//                ->setEndTime(new \DateTime())
//                ->setLastOnline(new \DateTime());
//            $em->persist($repo[0]);
//            $em->flush();
//        }

        dump(1);
        exit();

    }


}