<?php
/**
 * Created by PhpStorm.
 * User: cengi
 * Date: 24.04.2019
 * Time: 10:16
 */

namespace App\Controller;


use App\Asterisk\Entity\Cdr;
use App\Asterisk\Entity\QueueLog;
use App\Asterisk\Entity\IvrLogs;
use App\Entity\UserProfile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class igdasDetailController extends AbstractController
{
    /**
     * @IsGranted("ROLE_IGDAS_DETAIL")
     * @Route("/igdasss", name="igdas_details")
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
        return $this->render('igdas_detail/index.html.twig', [
            "form" => $form->createView(),]);
    }

    /**
     * @Route("/igdasdetails")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function igdas(Request $request)
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
            if ($formValidate["form"]["firstDate"] == $formValidate["form"]["lastDate"] && $formValidate["form"]["time"] == 86400){
                $rows = $this->igdasDetail($firstTime, $lastTime);
                if (count($rows) > 0) {
                    foreach ($rows as $row) {
                        $row["dateRange"] = date('Y-m-d', strtotime($formValidate["form"]["firstDate"] . "00:00:00"));
                        $row["dateRangeTime"] = date('H:i:s', strtotime($formValidate["form"]["firstDate"] . "00:00:00")) . " - " . date('H:i:s', strtotime($formValidate["form"]["lastDate"] . "23:59:59"));

                        $arr[] = $row;
                    }
                }
            }else {
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
                    $rows = $this->igdasDetail(date('Y-m-d H:i:s', $prevDate),
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
    public function igdasDetail($firstTime,$lastTime)
    {
        $em = $this->getDoctrine()->getManager();
        $igds = $em->getRepository(IvrLogs::class)
            ->createQueryBuilder('igd')
            ->select("igd.callId")
            ->where('igd.dt BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $firstTime)
            ->setParameter('endDate', $lastTime)
            ->getQuery()->getArrayResult();


        $item = [];
        $itemColumn = [];
        $idCall = "";
        foreach ($igds as $value) {
            if ($idCall == $value['callId']){
                $itemColumn = $value;
            }else{
                if (count($itemColumn) > 0){
                    $item []= $itemColumn;
                }
                $idCall = $value['callId'];
            }
        }


        $row = [];
        $rec = [];
        foreach ($item as $igd) {

            $cdr = $em->getRepository(Cdr::class)->createQueryBuilder("c")
                ->where("c.callId=:callId")
                ->andWhere("c.lastapp=:lastapp")
                ->andWhere("c.calldate BETWEEN :startDate AND :endDate")
                ->setParameters([
                    "callId"=>$igd["callId"],
                    "lastapp"=>"Queue",
                    "startDate"=>$firstTime,
                    "endDate"=>$lastTime
                ])->getQuery()->getResult();

            if (count($cdr) > 0) {
                $rec [] = $cdr;
            }
        }
        foreach ($rec as $item) {
            foreach ($item as $value) {
                if ($value->getDisposition() == "ANSWERED") {
                    if (strlen($value->getDst()) == 10) {
                        $usp = $em->getRepository(UserProfile::class)
                            ->createQueryBuilder('up')
                            ->select('up.firstName,up.lastName')
                            ->where('up.extension=:extension')
                            ->setParameter('extension', $value->getDst())
                            ->getQuery()->getArrayResult();
                        if (count($usp) > 0) {
                            $karsilayan = $usp[0]['firstName'] . " " . $usp[0]['lastName'];
                        } else {
                            $karsilayan = $value->getDst();
                        }
                    } else {
                        $karsilayan = $value->getDst();
                    }

                    $queuelog = $em->getRepository(QueueLog::class)->createQueryBuilder("q")
                        ->where("q.callid=:callid")
                        ->andWhere("q.created BETWEEN :startDate AND :endDate")
                        ->andWhere("q.event=:event")
                        ->setParameters([
                            "callid"=>$value->getUniqueid(),
                            "startDate"=>$firstTime,
                            "endDate"=>$lastTime,
                            "event"=>"COMPLETECALLER"
                        ])
                        ->getQuery()->getResult();

                    if (count($queuelog) > 0){
                        $duration = $value->getDuration();

                        $kuyrukSuresi = $queuelog[0]->getData1();
                        $konusmaSuresi = $queuelog[0]->getData2();

                        $ivrSuresi = $duration - ($kuyrukSuresi + $konusmaSuresi);


                        $baslangicZamani = date_format($value->getCalldate(), 'd-m-Y H:i:s');
                        $convert = strtotime($baslangicZamani);
                        $convertBitisZamani = $convert + $duration;
                        $bitisZamani = date('d-m-Y H:i:s', $convertBitisZamani);


                        $kuyrukgiris = date_format($queuelog[0]->getCreated(), 'd-m-Y H:i:s');
                        $convertKbz = strtotime($kuyrukgiris);
                        $convertKuyrukBitisZamani = $convertKbz + $kuyrukSuresi;
                        $kuyrukBitisZamani = date('d-m-Y H:i:s', $convertKuyrukBitisZamani);

                        $row[] = [
                            "dateRangeTime" => " ",
                            "arayannumara" => $value->getDst(),
                            "aranannumara" => "beyazmasa",
                            "kuyruksuresi" => $kuyrukSuresi,
                            "ivrsuresi" => $ivrSuresi,
                            "calmasuresi" => 3,
                            "konusmasuresi" => $konusmaSuresi,
                            "ivrmenusecim" => "IGDAS",
                            "kuyruk" => "IGDAS",
                            "karsilayan" => $karsilayan,
                            "baslangicZamani" => $baslangicZamani,
                            "bitisZamani" => $bitisZamani,
                            "kgiris" => $kuyrukgiris,
                            "kbitis" => $kuyrukBitisZamani

                        ];
                    }
                    else{
                        $queuelog = $em->getRepository(QueueLog::class)->createQueryBuilder("q")
                            ->where("q.callid=:callid")
                            ->andWhere("q.created BETWEEN :startDate AND :endDate")
                            ->andWhere("q.event=:event")
                            ->setParameters([
                                "callid"=>$value->getUniqueid(),
                                "startDate"=>$firstTime,
                                "endDate"=>$lastTime,
                                "event"=>"COMPLETEAGENT"
                            ])
                            ->getQuery()->getResult();

                        if (count($queuelog) > 0){
                            $duration = $value->getDuration();

                            $kuyrukSuresi = $queuelog[0]->getData1();
                            $konusmaSuresi = $queuelog[0]->getData2();

                            $ivrSuresi = $duration - ($kuyrukSuresi + $konusmaSuresi);


                            $baslangicZamani = date_format($value->getCalldate(), 'd-m-Y H:i:s');
                            $convert = strtotime($baslangicZamani);
                            $convertBitisZamani = $convert + $duration;
                            $bitisZamani = date('d-m-Y H:i:s', $convertBitisZamani);


                            $kuyrukgiris = date_format($queuelog[0]->getCreated(), 'd-m-Y H:i:s');
                            $convertKbz = strtotime($kuyrukgiris);
                            $convertKuyrukBitisZamani = $convertKbz + $kuyrukSuresi;
                            $kuyrukBitisZamani = date('d-m-Y H:i:s', $convertKuyrukBitisZamani);

                            $row[] = [
                                "dateRangeTime" => " ",
                                "arayannumara" => $value->getDst(),
                                "aranannumara" => "beyazmasa",
                                "kuyruksuresi" => $kuyrukSuresi,
                                "ivrsuresi" => $ivrSuresi,
                                "calmasuresi" => 3,
                                "konusmasuresi" => $konusmaSuresi,
                                "ivrmenusecim" => "IGDAS",
                                "kuyruk" => "IGDAS",
                                "karsilayan" => $karsilayan,
                                "baslangicZamani" => $baslangicZamani,
                                "bitisZamani" => $bitisZamani,
                                "kgiris" => $kuyrukgiris,
                                "kbitis" => $kuyrukBitisZamani

                            ];
                        }
                    }

                }
            }
        }
        return $row;
    }

}