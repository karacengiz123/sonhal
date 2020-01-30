<?php
/**
 * Yasin KARABULAK <yasinkarabulak@gmail.com>
 * 4/15/19 1:00 PM
 */

namespace App\Controller;

use App\Asterisk\Entity\QueueLog;
use App\Entity\AcwLog;
use App\Entity\AcwType;
use App\Entity\AgentBreak;
use App\Entity\Calls;
use App\Entity\RealtimeQueueMembers;
use App\Entity\User;
use App\Entity\UserProfile;
use App\Entity\WallMessages;
use App\Helpers\Date;
use Doctrine\ORM\Configuration;
use Grpc\Call;
use Psr\Log\NullLogger;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DashboardController
 * @package App\Controller
 * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
 */
class DashboardController extends AbstractController
{

    /**
     * @var string
     */
    private $startDate;

    /**
     * @var false|int
     */
    private $startDateAsTime;

    /**
     * @var string
     */
    private $endDate;

    /**
     * @var false|int
     */
    private $endDateAsTime;

    /**
     * DashboardController constructor.
     */
    public function __construct()
    {
//        $this->startDate = date('Y-m-d 00:00:00');
//        $this->endDate = date('Y-m-d 23:59:59');

        $this->startDate =  $this->startDate;
        $this->startDateAsTime = strtotime($this->startDate);
        $this->endDate = $this->endDate;
        $this->endDateAsTime = strtotime($this->endDate);
    }

    /**
     * @Route("/wallboard", name="dashboard")
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function index(Request $request): Response
    {


        $newdate =new \DateTime();
        $sDate=$newdate->format("Y-m-d 00:00:00");
        $eDate=$newdate->format("Y-m-d 23:59:59");



        // dashboard layout
        $leftColumnTop = array(
            'avail',
            'agentBreaks',
            'acw' => array(),
        );
        $leftColumnBottom = array();
        $midColumn = array();
        $rightColumn = array();

        // Entity manager ile Repository tanımlamaları
        $em = $this->getDoctrine()->getManager();
        $asteriskEm = $this->getDoctrine()->getManager('asterisk');
        $agentBreakRepository = $em->getRepository(AgentBreak::class);
        $acwTypesRepository = $em->getRepository(AcwType::class);
        $acwLogsRepository = $em->getRepository(AcwLog::class);
        $queueLogRepository = $asteriskEm->getRepository(QueueLog::class);
        $userProfileRepository = $em->getRepository(UserProfile::class);
        $callsRepository=$em->getRepository(Calls::class);
        $realtimeQueMember=$em->getRepository(RealtimeQueueMembers::class);
        $user=$em->getRepository(User::class);
        $wallMessagesRepo=$em->getRepository(WallMessages::class);


            $wallMessages=$wallMessagesRepo->createQueryBuilder("wm")
                ->select("wm.wallmessages")
                ->getQuery()->getResult();
        $wallMessages=end($wallMessages);
        foreach ($wallMessages as $wallMessage)
        {
            $wallMessage=$wallMessage;
        }

        // Moladaki agent Sayısı
        $agentBreaks = $user->createQueryBuilder("u")
            ->andwhere("u.state=:state")
            ->leftJoin("u.groups","groups")
            ->andWhere("groups.roles LIKE :roles")
            ->setParameters([
                "state"=>4,
                "roles"=>"%ROLE_INBOUND%"
            ])->getQuery()->getResult();
        $agentBreaks = count($agentBreaks);

        // ACW
        $acwTypes = $user->createQueryBuilder("u")
        ->andwhere("u.state=:state")
        ->leftJoin("u.groups","groups")
        ->andWhere("groups.roles LIKE :roles")
        ->setParameters([
            "state"=>2,
            "roles"=>"%ROLE_INBOUND%"
        ])->getQuery()->getResult();
        $acwTypes = count($acwTypes);

        // SORU
        $acwTypesQuestion = $user->createQueryBuilder("u")
            ->andwhere("u.state=:state")
            ->leftJoin("u.groups","groups")
            ->andWhere("groups.roles LIKE :roles")
            ->setParameters([
                "state"=>5,
                "roles"=>"%ROLE_INBOUND%"
            ])->getQuery()->getResult();
        $acwTypesQuestion = count($acwTypesQuestion);

        ////dışarama
        $acwTypesOutboud = $user->createQueryBuilder("u")
            ->andwhere("u.state=:state")
            ->leftJoin("u.groups","groups")
            ->andWhere("groups.roles LIKE :roles")
            ->setParameters([
                "state"=>6,
                "roles"=>"%ROLE_INBOUND%"
            ])->getQuery()->getResult();
        $acwTypesOutboud = count($acwTypesOutboud);

        ////diğer işlem tuşlamaları

        $acwTypesOtherAcws = $user->createQueryBuilder("u")
            ->andwhere("u.state=:state")
            ->leftJoin("u.groups","groups")
            ->andWhere("groups.roles LIKE :roles")
            ->setParameters([
                "state"=>11,
                "roles"=>"%ROLE_INBOUND%"
            ])->getQuery()->getResult();
        $acwTypesOtherAcws = count($acwTypesOtherAcws);


       //////KUYRUKTA BEKLEYEN CAĞRI SAYISI
        $inwaitingCalls=$callsRepository->createQueryBuilder("cl")
            ->where("cl.dtQueue is not null")
            ->andwhere("cl.queue is not null")
            ->andWhere("cl.dtExten is null")
            ->andwhere("cl.dt between :sDate and :eDate")
            ->andWhere("cl.callStatus=:status")
            ->andWhere("cl.callType=:ctype")
            ->setParameters(["sDate"=>$sDate, "eDate"=>$eDate,"status"=>"Active", "ctype"=>"Inbound"])
            ->getQuery()->getResult();
        $inwaitingCalls = count($inwaitingCalls);

        /////GELEN ÇAĞRI SAYISI
        ///
        $totalCalls=$callsRepository->createQueryBuilder("cl")
            ->where("cl.dt between :sDate and :eDate")
            ->andWhere("cl.callType=:ctype")
            ->andWhere("cl.callStatus=:status")
            ->setParameters(["sDate"=>$sDate, "eDate"=>$eDate, "ctype"=>"Inbound","status"=>"Done"])
            ->getQuery()->getResult();
        $totalCalls = count($totalCalls);


        //////24 sn den erken cevaplananlar
        $inboundCallList=$callsRepository->createQueryBuilder("cl")

            ->where("cl.dtExten between :sDate and :eDate")
            ->andWhere("cl.callType=:ctype")
            ->andWhere("cl.callStatus =:status")
            ->setParameters(["sDate"=>$sDate, "eDate"=>$eDate, "ctype"=>"Inbound","status"=>"Done"])

            ->getQuery()->getResult();


        $callWaitCounter=0;

        foreach($inboundCallList as $inboundCall)
        {

            if(($inboundCall->getDtExten() != null) and ($inboundCall->getDtQueue() != null))
            {
                //$callWait=$inboundCall->getDtQueue()->diff($inboundCall->getDtExten());
//                $diff=$inboundCall->getDtExten()->getTimestamp() - $inboundCall->getDtQueue()->getTimestamp();
                $diff=$inboundCall->getDurQueue();
                if($diff <= 24){ $callWaitCounter++;}
            }

        }


        /////Başarılı ÇAĞRI SAYISI
        ///
        $completedCalls=$callsRepository->createQueryBuilder("cl")
            ->where("cl.exten is not null")
            ->andWhere("cl.callStatus=:status")
            ->andWhere("cl.dt between :sDate and :eDate")
            ->andWhere("cl.callType=:ctype")
            ->setParameters(["status"=>"Done","sDate"=>$sDate, "eDate"=>$eDate, "ctype"=>"Inbound"])
            ->getQuery()->getResult();

        $completedCalls = count($completedCalls);


        /////Kaçan ÇAĞRI SAYISI
        ///
        $missedCalls=$callsRepository->createQueryBuilder("cl")
            ->where("cl.dtQueue is not null")
            ->andWhere("cl.exten is null")
            ->andWhere("cl.callStatus=:status")
            ->andWhere("cl.dt between :sDate and :eDate")
            ->andWhere("cl.callType=:ctype")
            ->setParameters(["status"=>"Done","sDate"=>$sDate, "eDate"=>$eDate, "ctype"=>"Inbound"])
            ->getQuery()->getResult();
        $missedCalls = count($missedCalls);

        /////IVR DA TAMAMLANAN ÇAĞRI SAYISI
        ///
        $toIvrEndedCalls=$callsRepository->createQueryBuilder("cl")
            ->where("cl.dt is not null")
            ->andWhere("cl.dtQueue is null")
            ->andWhere("cl.callStatus=:status")
            ->andWhere("cl.dt between :sDate and :eDate")
            ->andWhere("cl.callType=:ctype")
            ->setParameters(["status"=>"Done","sDate"=>$sDate, "eDate"=>$eDate, "ctype"=>"Inbound"])
            ->getQuery()->getResult();
        $toIvrEndedCalls = count($toIvrEndedCalls);

        /////aktif ÇAĞRI SAYISI
        ///
        $activeCalls=$callsRepository->createQueryBuilder("cl")
            ->where("cl.dtExten is not null")
            ->andWhere("cl.callStatus=:status")
            ->andWhere("cl.dtHangup is null")
            ->andWhere("cl.dt between :sDate and :eDate")
            ->andWhere("cl.callType=:ctype")
            ->setParameters(["status"=>"Active","sDate"=>$sDate, "eDate"=>$eDate, "ctype"=>"Inbound"])
            ->getQuery()->getResult();
        $activeCalls = count($activeCalls);

        /////outbound ÇAĞRI SAYISI
        ///
        $outboundCalls=$callsRepository->createQueryBuilder("cl")
            ->where("cl.callType=:ctype")
            ->andWhere("cl.dt between :sDate and :eDate")
            ->setParameters([ "sDate"=>$sDate, "eDate"=>$eDate,"ctype"=>"Outbound"])
            ->getQuery()->getResult();
        $outboundCalls = count($outboundCalls);

        /////outboundta konuşan agent sayısı
        ///
        $outboundCaller=$callsRepository->createQueryBuilder("cl")
            ->where("cl.callType=:ctype")
            ->andWhere("cl.dt between :sDate and :eDate")
            ->andWhere("cl.callStatus=:cStatus")
            ->setParameters([ "sDate"=>$sDate, "eDate"=>$eDate,"ctype"=>"Outbound","cStatus"=>"Active"])
            ->getQuery()->getResult();
        $outboundCaller = count($outboundCaller);


        //max wait time
        $maxWaitTimeArr=[];
        $maxWaitTimes=$callsRepository->createQueryBuilder("cl")
            ->where("cl.dt between :sDate and :eDate")
            ->andWhere("cl.callType=:ctype")
            ->andWhere("cl.callStatus=:callStatus")
            ->andWhere("cl.dtQueue IS NOT NULL")
            ->andWhere("cl.exten is null")
            ->setMaxResults(1)
            ->orderBy("cl.dtQueue","ASC")
            ->setParameters(["sDate"=>$sDate, "eDate"=>$eDate, "ctype"=>"Inbound","callStatus"=>"Active"])
            ->getQuery()->getResult();


        if(count($maxWaitTimes)>0)
        {
            $maxWaitTime = $maxWaitTimes[0]->getDtQueue();
            $nowTime = new \DateTime();
            $maxWaitTime= $maxWaitTime->diff($nowTime);
            $maxWaitTime= $maxWaitTime->format("%I:%S");


        } else {$maxWaitTime="00:00";}

        $avail = $user->createQueryBuilder("u")
            ->where("u.state =:state")
            ->leftJoin("u.groups","groups")
            ->andWhere("groups.roles LIKE :roles")
            ->setParameters([
                "state"=>1,
                "roles"=>"%ROLE_INBOUND%"
            ])
            ->getQuery()->getResult();
        $avail = count($avail);


        //TOPLAM INBOUND VE OUTBOUND CAGRIDAKI KİŞİ SAYISI
        $inCalls = $user->createQueryBuilder("u")
            ->andwhere("u.state=:state")
            ->leftJoin("u.groups","groups")
            ->andWhere("groups.roles LIKE :roles")
            ->setParameters([
                "state"=>8,
                "roles"=>"%ROLE_INBOUND%"
            ])->getQuery()->getResult();
        $inCalls = count($inCalls);

//            $inCalls = $inCalls->getQuery()->getResult();

        $leftColumnTop['agentBreaks'] = $agentBreaks;
        $leftColumnTop['inCalls']=$inCalls;
        $leftColumnTop['avail']=$avail ;
        $leftColumnTop['acw']["ACW"] = $acwTypes;
        $leftColumnTop['acw']["SORU"] = $acwTypesQuestion;
        $leftColumnTop['acw']["DISARAMA"] = $acwTypesOutboud;
        $leftColumnTop['acw']["DIGERISLEMLER"] = $acwTypesOtherAcws;

        // leftColumnBottom

        $leftColumnBottom['calls'] = $totalCalls;
        $leftColumnBottom['completed'] = $completedCalls;
        $leftColumnBottom['abandoned'] = $missedCalls;
//        $leftColumnBottom['active'] = count($activeCallInbound);
        $leftColumnBottom['toIvrEnded'] = $toIvrEndedCalls;
        $leftColumnBottom['outbound'] = $outboundCalls;
        ///2. wallboard ekranı
        $leftColumnBottom['wallMessage'] = $wallMessages;




        // midColumn
            $midColumn['inwaiting'] = $inwaitingCalls;

            $midColumn['serviceLevel']=0;

            if($completedCalls!=0)
            $midColumn['serviceLevel'] = intval(($callWaitCounter/$completedCalls)*100);

            $midColumn['longestHoldTime'] = $maxWaitTime;

        //en cok cağrı alan agentlar
        $agentTopList = array();
        $agentTopList = $callsRepository->createQueryBuilder('ql')
            ->select('count(ql.user) as cnt', 'IDENTITY(ql.user )as user1 ')
            ->groupBy('ql.user')
            ->where('ql.callStatus = :callStatus')
            ->andwhere('ql.callType = :callType')
            ->andWhere('ql.dt BETWEEN :start AND :end')
            ->andWhere('ql.user is not null')
            ->leftJoin("ql.user","user")
            ->leftJoin("user.groups","groups")
            ->andWhere("groups.roles LIKE :roles")
            ->setParameters([
                'callStatus' => 'Done',
                'start' => $sDate,
                'end' => $eDate,
                'callType' =>'Inbound',
                "roles"=>"%ROLE_INBOUND%"
            ])
            ->groupBy('ql.user')
            ->orderBy('cnt', 'DESC')

            ->setMaxResults(10)
            ->getQuery()
            ->getArrayResult();

        $agentInfo = array();
        if (count($agentTopList) > 0) {
            foreach ($agentTopList as $key => $item) {
                $agentInfo = $userProfileRepository->findOneBy(['user' => $item['user1']]);

                $rightColumn[$key]['count'] = $item['cnt'];
                $rightColumn[$key]['firstName'] = $agentInfo->getFirstName();
                $rightColumn[$key]['lastName']  = $agentInfo->getLastName();
            }
        }

        // sayfa içerisindeki alanların değerleri.
        $pageData = [
            'leftTop' => $leftColumnTop,
            'leftBottom' => $leftColumnBottom,
            'midColumn' => $midColumn,
            'rightColumn' => $rightColumn,
        ];

        // ajax request varsa json data döndür, dinamik sayfa içeriği yenileme.
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse($pageData);
        }

        return $this->render('dashboard/index.html.twig', $pageData);


    }
}
