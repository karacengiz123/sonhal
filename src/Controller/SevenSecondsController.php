<?php
/**
 * Created by PhpStorm.
 * User: cengi
 * Date: 3.05.2019
 * Time: 11:38
 */

namespace App\Controller;


use App\Asterisk\Entity\Cdr;
use App\Asterisk\Entity\QueueLog;
use App\Asterisk\Entity\IvrLogs;
use App\Entity\BreakType;
use App\Entity\User;
use App\Entity\UserProfile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SevenSecondsController extends AbstractController
{

    /**
     * @IsGranted("ROLE_SEVEN_SECOND")
     * @Route("/sevenSecond", name="seven_second")
     */
    public function form(Request $request)
    {
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
            ->add("time", ChoiceType::class, [
//                "label" => "Saat Aralıkları",
                "label" => " ",
                'choices' => [
                    "1 Günlük Aralıklarla" => 24 * 60 * 60,
//                    "15 Dakikalık Aralıklarla" => 15 * 60,
//                    "30 Dakikalık Aralıklarla" => 30 * 60,
//                    "1 Saatlik Aralıklarla" => 60 * 60,
//                    "1 Aylık Aralıklarla" => 30 * 24 * 60 * 60,
                ],
                "attr" => ["class" => "form-control",
                    "hidden"=>true
                ]
            ])
            ->getForm();
        return $this->render('seven_second/index.html.twig', [
            "form" => $form->createView(),]);
    }

    /**
     * @Route("/sevenSeconds")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function seven(Request $request)
    {
        $formValidate = $request->request->all();
        $firstTime = date('Y-m-d H:i:s', strtotime($formValidate["form"]["firstDate"] . "00:00:00"));
        $lastTime = date('Y-m-d H:i:s', strtotime($formValidate["form"]["lastDate"] . "23:59:59"));
        $time = $formValidate["form"]["time"];

//        return $this->json(date('Y-m-d H:i:s',1555020000));
        if ($formValidate["form"]["firstDate"] == "" && $formValidate["form"]["lastDate"] == "") {
            return new Response("Lütfen bir tarih aralığı seçiniz..");
        } else {
            $arr = [];
            if ($formValidate["form"]["firstDate"] == $formValidate["form"]["lastDate"] && $formValidate["form"]["time"] == 86400) {
                $rows = $this->sevenSeconds($firstTime, $lastTime);
                if (count($rows) > 0) {
                    foreach ($rows as $row) {
                        $row["dateRange"] = date('Y-m-d', strtotime($formValidate["form"]["firstDate"] . "00:00:00"));
                        $row["dateRangeTime"] = date('H:i:s', strtotime($formValidate["form"]["firstDate"] . "00:00:00")) . " - " . date('H:i:s', strtotime($formValidate["form"]["lastDate"] . "23:59:59"));

                        $arr[] = $row;
                    }
                }
            } else {
                $lastTime=date("Y-m-d H:i:s",strtotime($lastTime)+86400);

                $ranges = range(
                    strtotime($firstTime),
                    strtotime($lastTime),
                    $time
                );


                $prevDate = null;
                $arr = [];
                foreach ($ranges as $range) {
                    if ($prevDate == null) {
                        $prevDate = $range;
                        continue;
                    }
                    $rows = $this->sevenSeconds(date('Y-m-d H:i:s', $prevDate),
                        date('Y-m-d H:i:s', $range - 1));
                    if (count($rows) > 0) {
                        foreach ($rows as $row) {
                            $row["dateRange"] = date('Y-m-d', $prevDate);
                            $row["dateRangeTime"] = date('H:i:s', $prevDate) . " - " .date('H:i:s', $range - 1);
                            $arr[] = $row;
                        }
                    }
                    $prevDate = $range;
                }
            }
            return $this->json($arr);
        }

    }

    /**
     * @param $firstTime
     * @param $lastTime
     * @return array
     */
    public function sevenSeconds($firstTime, $lastTime)
    {


        $em = $this->getDoctrine()->getManager();


        $ivrLogs = $em->getRepository(IvrLogs::class)
            ->createQueryBuilder('ss')
            ->where('ss.dt BETWEEN :startDate AND :endDate')
            ->setParameters([
                'startDate' => $firstTime,
                'endDate' => $lastTime
            ])
            ->getQuery()->getArrayResult();//burası ıcın cokda onemlı degıl ne verdıgın her sekılde result ver.array ıcınde obje bır degısken alabılıyorum

        $item = [];
        $itemColumn = [];
        $idCall = "";
        foreach ($ivrLogs as $value) {
            if ($idCall == $value['callId']) {
                $itemColumn = $value;
            } else {
                if (count($itemColumn) > 0) {
                    $item [] = $itemColumn;
                }
                $idCall = $value['callId'];
            }
        }

        $sevenSeconds = [];
        foreach ($item as $ivrLog) {
            $detailCdr = $em->getRepository(Cdr::class)
                ->createQueryBuilder('c')
                ->select("c.calldate,c.src,c.duration,c.uniqueid,c.disposition")
                ->where("c.callId=:callId")
                ->andWhere("c.lastapp=:lastapp")
                ->andWhere('c.calldate BETWEEN :startDate AND :endDate')
                ->setParameters([
                    "callId" => $ivrLog['callId'],
                    "lastapp" => "Queue",
                    "startDate" => $firstTime,
                    "endDate" => $lastTime
                ])
                ->orderBy("c.cdrId", "DESC")
                ->getQuery()->getArrayResult();


            if (count($detailCdr) > 0) {
                $duration = 0;
                $src = "";
                $whoclosed = "";

                $duration = $detailCdr[0]["duration"];

                if ($src == "") {
                    $src = $detailCdr[0]["src"];
                }
                if ($detailCdr[0]["disposition"] == "ANSWERED") {

                    $queue_log = $em->getRepository(QueueLog::class)->createQueryBuilder("ql")
                        ->select("ql.event,ql.queuename,ql.agent,ql.data1,ql.data2")
                        ->where("ql.callid=:callid")
                        ->andWhere('ql.created BETWEEN :startDate AND :endDate')
                        ->andWhere("ql.event=:event")
                        ->setParameters([
                            "callid" => $detailCdr[0]["uniqueid"],
                            "startDate" => $firstTime,
                            "endDate" => $lastTime,
                            "event"=>"COMPLETECALLER"
                        ])
                        ->getQuery()->getArrayResult();

                    if (count($queue_log) > 0) {
                        $kuyrukBeklemeSuresi = $queue_log[0]["data1"];
                        $konusmaSuresi = $queue_log[0]["data2"];
                        $ivrBeklemeSuresi = $duration - ($kuyrukBeklemeSuresi + $konusmaSuresi);
                        if ($konusmaSuresi < 8) {
                            $baslangicZamani = date_format($detailCdr[0]["calldate"], 'd-m-Y H:i:s');
                            $convert = strtotime($baslangicZamani);
                            $convertBitisZamani = $convert + $duration;
                            $bitisZamani = date('d-m-Y H:i:s', $convertBitisZamani);

                            $usp = $em->getRepository(UserProfile::class)->findBy(["extension" => $queue_log[0]['agent']]);

                            if (count($usp) > 0) {
                                $sevenSeconds[] = [
                                    "dateRange" => " ",
                                    "dateRangeTime" => " ",
                                    "caller" => $src,
                                    "internal" => $queue_log[0]['agent'],
                                    "username" => $usp[0]->getUser()->getUsername(),
                                    "tc" => $usp[0]->getUser()->getUserProfile()->getTckn(),
                                    "name" => $usp[0]->getFirstName()." ".$usp[0]->getLastName() ,
                                    "speaktime" => $konusmaSuresi,
                                    "whoclosed" => "Vatandas"
                                ];
                            }
                        }
                    }else{
                        $queue_log = $em->getRepository(QueueLog::class)->createQueryBuilder("ql")
                            ->select("ql.event,ql.queuename,ql.agent,ql.data1,ql.data2")
                            ->where("ql.callid=:callid")
                            ->andWhere('ql.created BETWEEN :startDate AND :endDate')
                            ->andWhere("ql.event=:event")
                            ->setParameters([
                                "callid" => $detailCdr[0]["uniqueid"],
                                "startDate" => $firstTime,
                                "endDate" => $lastTime,
                                "event"=>"COMPLETEAGENT"
                            ])
                            ->getQuery()->getArrayResult();

                        if (count($queue_log) > 0) {
                            $kuyrukBeklemeSuresi = $queue_log[0]["data1"];
                            $konusmaSuresi = $queue_log[0]["data2"];
                            $ivrBeklemeSuresi = $duration - ($kuyrukBeklemeSuresi + $konusmaSuresi);
                            if ($konusmaSuresi < 8) {
                                $baslangicZamani = date_format($detailCdr[0]["calldate"], 'd-m-Y H:i:s');
                                $convert = strtotime($baslangicZamani);
                                $convertBitisZamani = $convert + $duration;
                                $bitisZamani = date('d-m-Y H:i:s', $convertBitisZamani);

                                $usp = $em->getRepository(UserProfile::class)->findBy(["extension" => $queue_log[0]['agent']]);

                                if (count($usp) > 0) {
                                    $sevenSeconds[] = [
                                        "dateRange" => " ",
                                        "dateRangeTime" => " ",
                                        "caller" => $src,
                                        "internal" => $queue_log[0]['agent'],
                                        "username" => $usp[0]->getUser()->getUsername(),
                                        "tc" => $usp[0]->getUser()->getUserProfile()->getTckn(),
                                        "name" => $usp[0]->getFirstName()." ".$usp[0]->getLastName() ,
                                        "speaktime" => $konusmaSuresi,
                                        "whoclosed" => "Temsilci"
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        }
        return $sevenSeconds;
    }
}