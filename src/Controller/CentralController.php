<?php

namespace App\Controller;

use App\Asterisk\Entity\QueueLog;
use App\Asterisk\Entity\Queues;
use App\Entity\Calls;
use App\Entity\User;
use App\Helpers\Date;
use Doctrine\ORM\Query;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function GuzzleHttp\Promise\queue;

class CentralController extends AbstractController
{

    private $columnsControl;

    public function __construct()
    {
        $this->columnsControl = false;
    }

    /**
     * @IsGranted("ROLE_CENTRAL_INDEX")
     * @Route("/central",name="central_index")
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function central(Request $request)
    {
        $asteriskEm = $this->getDoctrine()->getManager('asterisk');
        $quelog = $asteriskEm->getRepository(QueueLog::class);

        $form = $this->createFormBuilder()
            ->add("firstDate", DateType::class, [
                "label" => "İlk Tarih",
                "attr" => ["class" => "form-control"],
                'widget' => 'single_text'
            ])
            ->add("lastDate", DateType::class, [
                "label" => "Son Tarih",
                "attr" => ["class" => "form-control"],
                'widget' => 'single_text'
            ])
            ->add("queue", ChoiceType::class, [
                "label" => "Kuyruk Seçiniz",
                "attr" => ["class" => "form-control select2"],
//                'multiple'  => true,
            ])
            ->add("time", ChoiceType::class, [
                "label" => "Saat Aralıkları",
                "attr" => ["class" => "form-control select2"]
            ])
            ->getForm();


        $form->handleRequest($request);
        $row = array();

        return $this->render('central/index2.html.twig', [
            "form" => $form->createView(),
            "rows" => $row,
        ]);
    }

    /**
     * @Route("/central-data-select", name="central_data_select")
     */
    public function centralDataSelect(Request $request)
    {

        $queue = $this->getDoctrine()->getRepository(Queues::class)->findAll();

        $queues = [];
        $queues[] = ["id" => "selectQueues", "text" => "Kuyruk Seçiniz"];
        $queues[] = ["id" => "allQueues", "text" => "Tüm Kuyruklar"];
        foreach ($queue as $item) {
            $queues [] = ["id" => $item->getQueue(), "text" => $item->getDescription()];
        }

        $timeRange = [];
        if ($request->get("queue") == "allQueues") {
            $timeRange = [
//                ["id"=>15 * 60, "text" => "15 Dakikalık Aralıklarla"],
                ["id" => 24 * 60 * 60, "text" => "1 Günlük Aralıklarla"],
                ["id" => 30 * 24 * 60 * 60, "text" => "1 Aylık Aralıklarla"],
            ];
        }
        if ($request->get("queue") == "selectQueues") {
            $timeRange = [];
        } elseif (is_numeric($request->get("queue"))) {
            $timeRange = [
                ["id" => 24 * 60 * 60, "text" => "1 Günlük Aralıklarla"],
                ["id" => 15 * 60, "text" => "15 Dakikalık Aralıklarla"],
//                ["id"=>30 * 60, "text" => "30 Dakikalık Aralıklarla"],
                ["id" => 60 * 60, "text" => "1 Saatlik Aralıklarla"],
//                ["id"=>30 * 24 * 60 * 60, "text" => "1 Aylık Aralıklarla"],
            ];
        }

        return $this->json([
            "queues" => $queues,
            "timeRange" => $timeRange,
        ]);
    }


    /**
     * @Route("/central-detail",name="central_detail")
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function centralRapor(Request $request)
    {


        /**
         * Formdan gelen verilere göre santral raporu oluşturulacak.
         *
         * Belirlenen tarih aralığında, seçili kuyruk için talep edilen periyodlarla raporun oluşturulması talep edilmektedir.
         *
         */
        $formValidate = $request->request->all();
        $firstTime = date('Y-m-d H:i:s', strtotime($formValidate["form"]["firstDate"] . "00:00:00"));
        $lastTime = date('Y-m-d H:i:s', strtotime($formValidate["form"]["lastDate"] . "23:59:59"));
        $time = $formValidate["form"]["time"];
        $queuePost = $formValidate["form"]["queue"];

        /**
         * Formun doğruluğu kontrol ediliyor.
         */
        if ($formValidate["form"]["firstDate"] == "" && $formValidate["form"]["lastDate"] == "") {
            /**
             * formdaki veriler boş ise  uyarı gönder.
             */
            return new Response("Lütfen bir tarih aralığı seçiniz..");
        } else {
            $row=[];
            /**
             * formdaki 1. ve 2 gün birbirine eşit ise işlem yap 86400 gunluk seçimini sanıyeye esıtlıyor
             */
            if ($formValidate["form"]["firstDate"] == $formValidate["form"]["lastDate"] && $formValidate["form"]["time"] == 86400) {
                /**
                 * tum kuyruklar secili ise
                 */
                if ($queuePost == "allQueues") {
                    /**
                     * tüm kuyrukları getıren sorgu
                     */
                    $queues = $this->getDoctrine()->getManager('asterisk')
                        ->getRepository(Queues::class)->findAll();

                } else {
                    /**
                     * değilse seçilen,post edilen  kuyrugu  alıyor
                     */
                    $queues = $this->getDoctrine()->getManager('asterisk')
                        ->getRepository(Queues::class)->findBy(["queue" => $queuePost]);
                }
                /**
                 * kuyrukları parçalıyor
                 */
                foreach ($queues as $queue) {
                    /**
                     * en alttaki query sorgusunu çaüırıyor
                     */
                    $data = $this->queueAndDate($queue->getQueue(), $firstTime, $lastTime);
//dump($data);
//exit();
                    foreach ($data as $d) {
                        $row[] = [
                            "dateRange" => date('Y-m-d', strtotime($formValidate["form"]["firstDate"] . "00:00:00")),
                            "dateRangeTime" => date('H:i:s', strtotime($formValidate["form"]["firstDate"] . "00:00:00")) . " - " . date('H:i:s', strtotime($formValidate["form"]["lastDate"] . "23:59:59")),
                            "kuyruk" => $queue->getDescription(),
                            "totalCallEnterqueue" => $d["totalCallEnterqueue"],
                            "totalCallConnect" => $d["totalCallConnect"],
                            "totalCallAbandon" => $d["totalCallAbandon"],
                            "callConnectIn10" => $d["callConnectIn10"],
                            "callConnectIn20" => $d["callConnectIn20"],
//                            "callConnectInc20v30" => $d["callConnectInc20v30"],
                            "callConnectOn30" => $d["callConnectOn30"],
                            "totalCallAbandon10" => $d["totalCallAbandon10"],
//                            "totalCallAbandon10v20" => $d["totalCallAbandon10v20"],
                            "avgAgentDelegationTime" => gmdate("H:i:s",$d["AgentDelegationTime"])?? '00:00:00',
                            "missedCallToQueue" => gmdate("H:i:s",$d["missedCallToQueue"])?? '00:00:00',
                            "maxStandbyTimeQueue" => gmdate("H:i:s",$d["maxStandbyTimeQueue"])?? '00:00:00',
                            "serviceLevel2" => "%" . round($d["totalCallConnect"] ? $d["callConnectIn20"] / $d["totalCallConnect"] : 0,2)*100,
                            "accessibility" => "%" . round($d["totalCallEnterqueue"] ? $d["totalCallConnect"] / $d["totalCallEnterqueue"]: 0,2)*100,

                        ];
                    }

//                    /**
//                     * columns 0 dan buyukse columns u column a eşitliyor
//                     */
////                    if (count($columns) > 0) {
////                        $column = $columns;
////                    }
//                    /**
//                     * row dan gelen verılerı  "" gibi değişkenlere atanıp template ıcın uyarlanıyor
//                     *  +row bir sonrakı row larıda listelemek için
//                     */
                }
            } /**
             *  1. ve 2. gün içerisindeki veriler aynı gün değilse range ile bir değişkene atıyor
             */
            else {
                $ranges = range(
                    strtotime($firstTime),
                    strtotime($lastTime),
                    $time
                );
                /**
                 * onceki gunu null yapıyor
                 */
                $prevDate = null;
                /**
                 * tum kuyruklar secılı ıse
                 */
                if ($queuePost == "allQueues") {
                    /**
                     * tum kuyrukları getıren sorgu
                     */
                    $queues = $this->getDoctrine()->getManager('asterisk')
                        ->getRepository(Queues::class)->findAll();
                } else {
                    /**
                     * değilse tek kuyrugu getıren sorgu
                     */
                    $queues = $this->getDoctrine()->getManager('asterisk')
                        ->getRepository(Queues::class)->findBy(["queue" => $queuePost]);
                }
                /**
                 * column dıye bos bır dızı olusturuluyor
                 */
                $column = [];
                /**
                 * kuyruk  parçalanıyor
                 */

                foreach ($queues as $queue) {
                    /**
                     * ranges parçalanıyor
                     */
                    foreach ($ranges as $range) {
                        /**
                         * baslangıcın null olup olmadıgı soruluyor
                         */
                        if ($prevDate == null) {
                            /**
                             * null ıse
                             * baslangıcı range e esıtlıyor
                             * ve devam edıyor
                             */
                            $prevDate = $range;
                            continue;
                        }
                        /**
                         * queueAndDate fonks ilk gun son gun -1 olacak sekilde çağırılıyor
                         */
//                        list($rows, $columns) = $this->queueAndDate($queue->getQueue(), date('Y-m-d H:i:s', $prevDate),
//                            date('Y-m-d H:i:s', $range - 1));
                        $data = $this->queueAndDate($queue->getQueue(), date('Y-m-d H:i:s', $prevDate),
                            date('Y-m-d H:i:s', $range - 1));

                        /**
                         * columns birden fazla ise
                         *
                         */
//                        if (count($columns) > 0) {
//                            $column = $columns;
//                        }
                        /**
                         * row dızısının ıcıne tarıh aralıkları  kuyruklar eklenıyor ve bır sonrakı row da eklenıyor
                         */
                        foreach ($data as $d) {
                            $row[] = [
                                "dateRange" => date('Y-m-d', $prevDate),
                                "dateRangeTime" => date('H:i:s', $prevDate) . " - " . date('H:i:s', $range - 1),
                                "kuyruk" => $queue->getDescription(),
                                "totalCallEnterqueue" => $d["totalCallEnterqueue"],
                                "totalCallConnect" => $d["totalCallConnect"],
                                "totalCallAbandon" => $d["totalCallAbandon"],
                                "callConnectIn10" => $d["callConnectIn10"],
                                "callConnectIn20" => $d["callConnectIn20"],
//                                "callConnectInc20v30" => $d["callConnectInc20v30"],
                                "callConnectOn30" => $d["callConnectOn30"],
                                "totalCallAbandon10" => $d["totalCallAbandon10"],
//                                "totalCallAbandon10v20" => $d["totalCallAbandon10v20"],
                                "avgAgentDelegationTime" => gmdate("H:i:s",$d["AgentDelegationTime"])?? '00:00:00',
                                "missedCallToQueue" => gmdate("H:i:s",$d["missedCallToQueue"])?? '00:00:00',
                                "maxStandbyTimeQueue" => gmdate("H:i:s",$d["maxStandbyTimeQueue"])?? '00:00:00',
                                "serviceLevel2" => "%" . round($d["totalCallEnterqueue"] ? $d["callConnectIn20"] / $d["totalCallEnterqueue"] : 0,2)*100,
                                "accessibility" => "%" . round($d["totalCallEnterqueue"] ? $d["totalCallConnect"] / $d["totalCallEnterqueue"]: 0,2)*100,

                            ];
//                            + $rows;
                            /**
                             * ılk gunu son gune esıtlenıyor
                             */
                            $prevDate = $range;

                        }
                    }
                }
            }

            /**
             * rowları ve column ları json olarak donuyor
             */
            return $this->json([
                "row" => $row,
//                "column" => $column
            ]);
        }
    }


    /**
     * @param $queue
     * @param $firstTime
     * @param $lastTime
     * @return mixed
     */
    public function queueAndDate($queue, $firstTime, $lastTime)
    {
        /**
         * queue,fırst,last parametlerı dısarıdan alınması ıcın fonk ıcıne  tanımlanıyor
         */
        $em = $this->getDoctrine()->getManager();
        $callsRepo = $em->getRepository(Calls::class);

//        $row = [];
//        $columns = [];
//        /**
//         * columnscontroll false ise columnsları yerlestırıyor
//         */
//        if ($this->columnsControl == false) {
//            $columns [] = ["data" => "dateRange", "name" => "dateRange", "title" => "TARİH"];
//            $columns [] = ["data" => "dateRangeTime", "name" => "dateRangeTime", "title" => "24/15 <br> DAKİKALIK <br> VEYA SAATLİK"];
//            $columns [] = ["data" => "kuyruk", "name" => "kuyruk", "title" => "KUYRUK"];
//        }


        ///                 Toplam Gelen Çağrı/// son sorguya selecte count ekle

        $totalEntCalls = "(select count(tc.idx) as totalEntCalls from " . Calls::class . "  tc
          where tc.callType = 'Inbound' and tc.callStatus = 'Done' and  tc.queue= '" . $queue . "'
              and tc.dt between '" . $firstTime . "' and '" . $lastTime . "' ) as totalCallEnterqueue";

        //////////////              Toplam cevaplanan cagrı
        $totalConCalls = "(select count(c2.idx) as totalConCalls from " . Calls::class . " c2
              where c2.queue= '" . $queue . "' and c2.exten is not null and c2.callType = 'Inbound' and c2.callStatus = 'Done'
              and c2.dt between '" . $firstTime . "' and '" . $lastTime . "'  ) as totalCallConnect";
//        if ($this->columnsControl == false) {
//
//            $columns [] = ["data" => "totalCallConnect", "name" => "totalCallConnect", "title" => "Toplam Cevaplanan Çağrı Sayısı"];
//        }

/////////////////////       TOPLAM KACAN CAGRI
        $totalAbandon = "(select count(callAbn.idx) as callAbandon from " . Calls::class . " callAbn
              WHERE callAbn.queue= '" . $queue . "' and callAbn.user is  null and callAbn.callType = 'Inbound' and callAbn.callStatus = 'Done'
              and callAbn.dt between '" . $firstTime . "' and '" . $lastTime . "'   ) as totalCallAbandon";
//        if ($this->columnsControl == false) {
//            $columns [] = ["data" => "totalCallAbandon", "name" => "totalCallAbandon", "title" => "Toplam Kaçan Çağrı Sayısı"];
//        }
////////////              10sn ıcınde cevaplanan
        $callConnectIn10 = "(select count(c10.idx) as ConnectIn10 FROM " . Calls::class . "   c10
            WHERE c10.queue= '" . $queue . "' and c10.durQueue<10 AND c10.user is not NULL and c10.callType='Inbound' and  c10.callStatus='Done'
            and c10.dt between '" . $firstTime . "' and '" . $lastTime . "') as callConnectIn10";
//        if ($this->columnsControl == false) {
//            $columns [] = ["data" => "callConnectIn10", "name" => "callConnectIn10", "title" => "10sn içinde Cevaplanan Çağrı Sayısı"];

//////////              20sn ıcınde cevaplanan
        $callConnectIn20 = "(select count(c20.idx) as ConnectIn20 FROM " . Calls::class . "   c20
            WHERE c20.queue ='" . $queue . "' and c20.durQueue<20 AND c20.user is not NULL and c20.callType='Inbound' and  c20.callStatus='Done' 
            and c20.dt between '" . $firstTime . "' and '" . $lastTime . "') as callConnectIn20";
        if ($this->columnsControl == false) {
            $columns [] = ["data" => "callConnectIn20", "name" => "callConnectIn20", "title" => "20sn içinde Cevaplanan Çağrı Sayısı"];
        }
//////////              20sn ve 30sn  ıcınde cevaplanan   BURADA 2 tabe Between kullanabılıormuyuz SOR
//            $callConnectInc20v30 = "(select count(c20v30.idx) as ConnectInc20v30 FROM " . Calls::class . "   c20v30
//            WHERE c20v30.queue ='" . $queue . "' and c20v30.user is not NULL and c20v30.callType='Inbound' and  c20v30.callStatus='Done'
//            and c20v30.dt between '" . $firstTime . "' and '" . $lastTime . "'
//            and c20v30.durQueue between 20 and 30) as callConnectInc20v30";

//            if ($this->columnsControl == false) {
//                $columns [] = ["data" => "callConnectInc20v30", "name" => "callConnectInc20v30", "title" => "20sn ile 30 içinde Cevaplanan Çağrı Sayısı"];
//            }
//////////              30 sn Üstü Toplam Cevaplanan Çağrı Sayısı

        $callConnectOn30 = "(select count(o30.idx) as ConnectOn30 FROM " . Calls::class . "   o30
            WHERE o30.queue = '" . $queue . "'  and o30.durQueue>30 AND o30.user is not NULL and o30.callType='Inbound' and  o30.callStatus='Done'
            and o30.dt between '" . $firstTime . "' and '" . $lastTime . "') as callConnectOn30";

//            if ($this->columnsControl == false) {
//                $columns [] = ["data" => "callConnectOn30", "name" => "callConnectOn30", "title" => "30sn Üstü Toplam  Cevaplanan Çağrı Sayısı"];
//            }
//////////             10 sn İçinde Toplam Kaçan Çağrı Sayısı

        $totalAbandon10 = "(select count(callAbn10.idx) as callAbandon10 from " . Calls::class . " callAbn10
             WHERE callAbn10.queue = '" . $queue . "' and callAbn10.durQueue<10 and callAbn10.user is  null and callAbn10.callType = 'Inbound' and callAbn10.callStatus = 'Done'
             and callAbn10.dt between '" . $firstTime . "' and '" . $lastTime . "' ) as totalCallAbandon10";

//            if ($this->columnsControl == false) {
//                $columns [] = ["data" => "totalCallAbandon10", "name" => "totalCallAbandon10", "title" => "10sn içinde Toplam Kaçan Çağrı Sayısı"];
//            }
//////////             10 - 20 sn İçinde Toplam Kaçan Çağrı Sayısı

//            $totalAbandon10v20 = "(select count(cAbn10v20.idx) as callAbandon10v20 from " . Calls::class . " cAbn10v20
//             where cAbn10v20.queue = '" . $queue . "' and   cAbn10v20.user is  null and cAbn10v20.callType = 'Inbound' and cAbn10v20.callStatus = 'Done'
//             and cAbn10v20.dt between '" . $firstTime . "' and '" . $lastTime . "'
//             and  cAbn10v20.durQueue between 10 and 20 ) as totalCallAbandon10v20";

//            if ($this->columnsControl == false) {
//                $columns [] = ["data" => "totalCallAbandon10v20", "name" => "totalCallAbandon10v20", "title" => "10sn ile 20sn içinde Toplam Kaçan Çağrı Sayısı"];
//            }
//////////             Temsilciye Aktarılana Kadarki Ort. Bekleme Süresi

        $avgAgentDelegationTime = "(select avg(delTime.durQueue) as delegationTime from " . Calls::class .  " delTime
            where delTime.queue= '" . $queue . "' and delTime.user is not null and delTime.callType = 'Inbound' and delTime.callStatus='Done'
            and delTime.dt between '" . $firstTime . "' and '" . $lastTime . "') as AgentDelegationTime";

//            if ($this->columnsControl == false) {
//                $columns [] = ["data" => "avgAgentDelegationTime", "name" => "avgAgentDelegationTime", "title" => "Temsilciye Aktarılana Kadarki Ort. Bekleme Sürsi Sn"];
//            }
//////////             Kuyrukta Beklerken Kapanan Çağrı Ort.  Süresi

        $missedCallToQueue = "(select avg(missCall.durQueue) as missedCallQueue from  " . Calls::class . " missCall
            where missCall.queue= '" . $queue . "' and missCall.user is null and missCall.callType = 'Inbound' and missCall.callStatus = 'Done'
            and missCall.dt between '" . $firstTime . "' and '" . $lastTime . "') as missedCallToQueue";

//            if ($this->columnsControl == false) {
//                $columns [] = ["data" => "missedCallToQueue", "name" => "missedCallToQueue", "title" => "Kuyrukta Beklerken Kapanan Çağrı   Ort. Bekleme Süresi Sn"];
//            }
//////////             Kuyurukta Bekleyen Çağrının Max Bekleme Süresi

        $maxStandbyTimeQueue = "(select max(maxStandby.durQueue) as standbyTimeQueue from  " . Calls::class . "  maxStandby
            where maxStandby.queue= '" . $queue . "' and maxStandby.callType = 'Inbound' and maxStandby.callStatus = 'Done'
            and maxStandby.dt between '" . $firstTime . "' and '" . $lastTime . "') as maxStandbyTimeQueue";

//            if ($this->columnsControl == false) {
//                $columns [] = ["data" => "maxStandbyTimeQueue", "name" => "maxStandbyTimeQueue", "title" => "Kuyrukta Bekleyen  Çağrının  Max Bekleme Süresi Sn"];
//            }
////////////////////////////////////SERVİS SEVİYESİ 2
//            $serviseLevel2=$callConnectIn20/$totalConCalls;

        ////////////////////////////////////ulaşılabilirlik oranı

//$accessibility=

        $query = $this->getDoctrine()->getManager()->createQuery('select   c.queue as queue, ' . $totalEntCalls . ',
        ' . $totalConCalls . ',' . $totalAbandon . ',' . $callConnectIn10 . ',' . $callConnectIn20 . ',' . $callConnectOn30 . ',' . $totalAbandon10 . ',
        ' . $avgAgentDelegationTime . ', ' . $missedCallToQueue . ',' . $maxStandbyTimeQueue . '
         from ' . Calls::class . '  as c where  c.queue='.$queue.' group by queue');

        return $query->getArrayResult();


//        dump($query);
//        exit();

//where c.dt between '".$firstTime."' and '".$lastTime."'
//dump($totalEntCalls);
//exit();
//        $query=$this->getDoctrine()->getManager()->createQuery(''.$totalEntCalls.' from ''');
//        exit();
//        return $query->getArrayResult();

//        dump($totalEntCalls);

//        exit();
//        $callsEnterque = $callsRepo->createQueryBuilder('c')
//            ->Where('c.dt BETWEEN :startDate AND :endDate')
//            ->andWhere('c.queue=:queue')
//            ->andWhere('c.callStatus=:callStatus')
//            ->andWhere("c.callType=:ctype")
//            ->setParameters([
//                "startDate" => $firstTime,
//                "endDate" => $lastTime,
//                "queue" => $queue,
//                "callStatus" => "Done",
//                "ctype" => "Inbound"
//            ])
//            ->getQuery()->getResult();
//
//        $row ["callsEnterque"] = count($callsEnterque);
//        if ($this->columnsControl == false) {
//            $columns [] = ["data" => "callsEnterque", "name" => "callsEnterque", "title" => "TOPLAM GELEN ÇAĞRI SAYISI"];
//        }
//
//        $callsConnect = $callsRepo->createQueryBuilder('c')
//            ->Where('c.dt BETWEEN :startDate AND :endDate')
//            ->andWhere('c.queue=:queue')
//            ->andWhere('c.callStatus=:callStatus')
//            ->andWhere('c.dtExten IS NOT NULL')
//            ->andWhere("c.callType=:ctype")
//            ->setParameters([
//                "startDate" => $firstTime,
//                "endDate" => $lastTime,
//                "queue" => $queue,
//                "callStatus" => "Done",
//                "ctype" => "Inbound"
//            ])
//            ->getQuery()->getResult();
//
//        $row ["callsConnect"] = count($callsConnect);
//        if ($this->columnsControl == false) {
//            $columns [] = ["data" => "callsConnect", "name" => "callsConnect", "title" => "TOPLAM CEVAPLANAN ÇAĞRI SAYISI"];
//        }
//
//        $callsAbandon = $callsRepo->createQueryBuilder('c')
//            ->Where('c.dt BETWEEN :startDate AND :endDate')
//            ->andWhere('c.queue=:queue')
//            ->andWhere('c.callStatus=:callStatus')
//            ->andWhere('c.dtExten IS NULL')
//            ->andWhere("c.callType=:ctype")
//            ->setParameters([
//                "startDate" => $firstTime,
//                "endDate" => $lastTime,
//                "queue" => $queue,
//                "callStatus" => "Done",
//                "ctype" => "Inbound"
//            ])
//            ->getQuery()->getResult();
//
//        $row ["callsAbandon"] = count($callsAbandon);
//        if ($this->columnsControl == false) {
//            $columns [] = ["data" => "callsAbandon", "name" => "callsAbandon", "title" => "TOPLAM KAÇAN ÇAĞRI SAYISI"];
//        }
//
//        $callsSec = $callsRepo->createQueryBuilder('c')
//            ->Where('c.dt BETWEEN :startDate AND :endDate')
//            ->andWhere('c.queue=:queue')
//            ->andWhere('c.dtExten is NOT NULL')
//            ->andWhere('c.callStatus=:callStatus')
//            ->andWhere("c.callType=:ctype")
//            ->setParameters([
//                "startDate" => $firstTime,
//                "endDate" => $lastTime,
//                "queue" => $queue,
//                "callStatus" => "Done",
//                "ctype" => "Inbound"
//            ])
//            ->getQuery()->getResult();
//
///////////////////////20 sanıye kadar cevaplanan cağrı
//        $callTwentySec = [];
//        foreach ($callsSec as $callSec) {
//            $diff = Date::diffDateTimeToSecond($callSec->getDtExten(), $callSec->getDtQueue());
//            if ($diff < 20) {
//                $callTwentySec [] = $diff;
//            }
//        }
//
//        $row ["callTwentySec"] = count($callTwentySec);
//        if ($this->columnsControl == false) {
//            $columns [] = ["data" => "callTwentySec", "name" => "callTwentySec", "title" => "20 SN İÇİNDE CEVAPLANAN ÇAĞRI SAYISI"];
//        }
///////////////////////10 sanıye kadar cevaplanan cağrı
//
//        $callTenSec = [];
//        foreach ($callsSec as $callSec) {
//            $diff = Date::diffDateTimeToSecond($callSec->getDtExten(), $callSec->getDtQueue());
//            if ($diff < 10) {
//                $callTenSec [] = $diff;
//            }
//        }
//
//        $row ["callsTenSec"] = count($callTenSec);
//        if ($this->columnsControl == false) {
//            $columns [] = ["data" => "callsTenSec", "name" => "callsTenSec", "title" => "10 SN İÇİNDE CEVAPLANAN ÇAĞRI SAYISI"];
//        }
///////////////////////10 ile 20 sanıye arasında cevaplanan cağrı
//
////        $callTenOrTwentySec = [];
////        foreach ($callsSec as $callSec) {
////            $diff = Date::diffDateTimeToSecond($callSec->getDtExten(), $callSec->getDtQueue());
////            if ($diff >= 10 and $diff < 20) {
////                $callTenOrTwentySec [] = $diff;
////            }
////        }
////        $row ["callTenOrTwentySec"] = count($callTenOrTwentySec);
////        if ($this->columnsControl == false) {
////            $columns [] = ["data" => "callTenOrTwentySec", "name" => "callTenOrTwentySec", "title" => "10 ile 20 arasında TOPLAM CEVAPLANAN ÇAĞRI SAYISI"];
////        }
//
//        /////////////////////20 ile 30 sanıye arasında cevaplanan cağrı
//
//        $callTwentyWithThirtySec = [];
//        foreach ($callsSec as $callSec) {
//            $diff = Date::diffDateTimeToSecond($callSec->getDtExten(), $callSec->getDtQueue());
//            if ($diff >= 20 and $diff <= 30) {
//                $callTwentyWithThirtySec [] = $diff;
//            }
//        }
//        $row ["callTwentyWithThirtySec"] = count($callTwentyWithThirtySec);
//        if ($this->columnsControl == false) {
//            $columns [] = ["data" => "callTwentyWithThirtySec", "name" => "callTwentyWithThirtySec", "title" => "20 ile 30 arasında TOPLAM CEVAPLANAN ÇAĞRI SAYISI"];
//        }
//        /////////////////////30sn altı cevaplanan cağrı
//
////        $row["callin30seconds"]=$row ["callTwentyWithThirtySec"] + $row ["callTwentySec"] ;
////        if ($this->columnsControl == false) {
////            $columns [] = ["data" => "callin30seconds", "name" => "callin30seconds", "title" => "30sn altı TOPLAM CEVAPLANAN ÇAĞRI SAYISI"];
////        }
//        /////////////////////30dan büyük sanıye arasında cevaplanan cağrı
//
//        $callThirtySec = [];
//        foreach ($callsSec as $callSec) {
//            $diff = Date::diffDateTimeToSecond($callSec->getDtExten(), $callSec->getDtQueue());
//            if ($diff > 30) {
//                $callThirtySec [] = $diff;
//            }
//        }
//        $row ["callThirtySec"] = count($callThirtySec);
//        if ($this->columnsControl == false) {
//            $columns [] = ["data" => "callThirtySec", "name" => "callThirtySec", "title" => "30sn üstü  TOPLAM CEVAPLANAN ÇAĞRI SAYISI"];
//        }
//
////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////10 sn ıcınde kacan cagrı sayısı
//        $callTenSecAbandon = [];
//        foreach ($callsAbandon as $callAbandon) {
//            $diff = Date::diffDateTimeToSecond($callAbandon->getDtHangUp(), $callAbandon->getDtQueue());
//            if ($diff < 10) {
//                $callTenSecAbandon [] = $diff;
//            }
//        }
//        $row ["callTenSecAbandon"] = count($callTenSecAbandon);
//        if ($this->columnsControl == false) {
//            $columns [] = ["data" => "callTenSecAbandon", "name" => "callTenSecAbandon", "title" => "10 sn içinde TOPLAM KAÇAN ÇAĞRI SAYISI"];
//        }
//////////////////////////////10 ile 20 sn ıcınde kacan cagrı sayısı
//        $callTenSecWithTwentyAbandon = [];
//        foreach ($callsAbandon as $callAbandon) {
//            $diff = Date::diffDateTimeToSecond($callAbandon->getDtHangUp(), $callAbandon->getDtQueue());
//            if ($diff >= 10 and $diff < 20) {
//                $callTenSecWithTwentyAbandon [] = $diff;
//            }
//        }
//        $row ["callTenSecWithTwentyAbandon"] = count($callTenSecWithTwentyAbandon);
//        if ($this->columnsControl == false) {
//            $columns [] = ["data" => "callTenSecWithTwentyAbandon", "name" => "callTenSecWithTwentyAbandon", "title" => "10 ile 20 sn arasında TOPLAM KAÇAN ÇAĞRI SAYISI"];
//        }
////////////////////////////// 20 ile 30  sn ıcınde kacan cagrı sayısı
//        $callTwentySecWithThirtyAbandon = [];
//        foreach ($callsAbandon as $callAbandon) {
//            $diff = Date::diffDateTimeToSecond($callAbandon->getDtHangUp(), $callAbandon->getDtQueue());
//            if ($diff >= 20 and $diff < 30) {
//                $callTwentySecWithThirtyAbandon [] = $diff;
//            }
//        }
////        $row ["callTwentySecWithThirtyAbandon"] = count($callTwentySecWithThirtyAbandon);
////        if ($this->columnsControl == false) {
////            $columns [] = ["data" => "callTwentySecWithThirtyAbandon", "name" => "callTwentySecWithThirtyAbandon", "title" => "20 ile 30 sn arasında TOPLAM KAÇAN ÇAĞRI SAYISI"];
////        }
//
//////////////#temsilciye aktarılana kadarki ort  bekleme suresi
//
//        $callSecAVG = [];
//        foreach ($callsSec as $callSec) {
//            $diff = Date::diffDateTimeToSecond($callSec->getDtExten(), $callSec->getDtQueue());
//            $callSecAVG [] = $diff;
//
//        }
//        $callSum = array_sum($callSecAVG);
//        $callCount = count($callSecAVG);
//
//        $row["callSecAVG"] = round($callCount ? $callSum / $callCount : 0, 2);
//
//        if ($this->columnsControl == false) {
//            $columns [] = ["data" => "callSecAVG", "name" => "callSecAVG", "title" => "Tesmilciye Aktarılana Kadarki Ort Bekleme Süresi Sn"];
//        }
//////////////Kuyrukta Beklerken kapanan  çağrı ort  suresi
//
//        $callSecAbandonAVG = [];
//        foreach ($callsAbandon as $callAbandon) {
//            $diff = Date::diffDateTimeToSecond($callAbandon->getDtHangUp(), $callAbandon->getDtQueue());
//            $callSecAbandonAVG [] = $diff;
//
//        }
//        $callSecAbandonSum = array_sum($callSecAbandonAVG);
//        $callSecAbandonCount = count($callSecAbandonAVG);
//
//        $row["callSecAbandonAVG"] = round($callSecAbandonCount ? $callSecAbandonSum / $callSecAbandonCount : 0, 2);
//
//        if ($this->columnsControl == false) {
//            $columns [] = ["data" => "callSecAbandonAVG", "name" => "callSecAbandonAVG", "title" => "Kuyrukta Beklerken Kapanan  Çağrı Ort Süresi Sn"];
//        }
////////////////kuyrukta bekleyen cagrının maksimum bekleme süresi
//
//        $callSecMAX = [];
//        foreach ($callsAbandon as $callAbandon) {
//            $diff = Date::diffDateTimeToSecond($callAbandon->getDtHangUp(), $callAbandon->getDtQueue());
//            $callSecMAX [] = $diff;
//
//        }
//
//        if (count($callSecMAX) > 0) {
//            $row["callSecMAX"] = max($callSecMAX);
//        } else {
//            $row["callSecMAX"] = 0;
//        }
//
//        if ($this->columnsControl == false) {
//            $columns [] = ["data" => "callSecMAX", "name" => "callSecMAX", "title" => "Kuyrukta Bekleyen Çağrının Maksimum Bekleme Süresi Sn"];
//        }
////////////////////////////kaçan cağrının maksimum kaçma süresi
////        $callSecAbandonMAX = [];
////        foreach ($callsAbandon as $callAbandon) {
////            $diff = Date::diffDateTimeToSecond($callAbandon->getDtHangUp(), $callAbandon->getDtQueue());
////            $callSecAbandonMAX [] = $diff;
////
////        }
////
////        if (count($callSecAbandonMAX) > 0) {
////            $row["callSecAbandonMAX"] = max($callSecAbandonMAX);
////        } else {
////            $row["callSecAbandonMAX"] = 0;
////        }
////
////        if ($this->columnsControl == false) {
////            $columns [] = ["data" => "callSecAbandonMAX", "name" => "callSecAbandonMAX", "title" => "Kaçan Çağrının Maksimum Kaçma Süresi Sn"];
////        }
//
////////////////////////////////////SERVİS SEVİYESİ 1
////        $row["serviseLevel1"] = "%" . round($row ["callsConnect"] ? $row ["callsTenSec"] / $row ["callsConnect"] : 0, 2) * 100;
////
////        if ($this->columnsControl == false) {
////            $columns [] = ["data" => "serviseLevel1", "name" => "serviseLevel1", "title" => "SERVİS SEVİYESİ 1"];
////        }
//
////////////////////////////////////SERVİS SEVİYESİ 2
//        $row["serviseLevel2"] = "%" . round($row ["callsConnect"] ? $row ["callTwentySec"] / $row ["callsConnect"] : 0, 2) * 100;
//
//        if ($this->columnsControl == false) {
//            $columns [] = ["data" => "serviseLevel2", "name" => "serviseLevel2", "title" => "SERVİS SEVİYESİ 2"];
//        }
//
////////////////////////////////////SERVİS SEVİYESİ 3
////        $row["serviseLevel3"] = "%" . round($row ["callsConnect"] ? ( $row ["callin30seconds"] / $row ["callsConnect"] ): 0, 2) * 100;
////
////        if ($this->columnsControl == false) {
////            $columns [] = ["data" => "serviseLevel3", "name" => "serviseLevel3", "title" => "SERVİS SEVİYESİ 3"];
////        }
//
////////////////////////////////////ulaşılabilirlik oranı
//        $row["accessibility"] = "%" . round($row ["callsEnterque"] ? $row ["callsConnect"] / $row ["callsEnterque"] : 0, 2) * 100;
//
//        if ($this->columnsControl == false) {
//            $columns [] = ["data" => "accessibility", "name" => "accessibility", "title" => "ULAŞILABİLİRLİK ORANI (AR)"];
//        }
//
//        $this->columnsControl = true;
//
//        return array($row, $columns);
    }


}