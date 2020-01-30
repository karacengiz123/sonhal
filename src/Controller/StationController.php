<?php
/**
 * Created by PhpStorm.
 * User: cengi
 * Date: 18.04.2019
 * Time: 12:44
 */

namespace App\Controller;


use App\Asterisk\Entity\Cdr;
use App\Asterisk\Entity\IvrLogs;
use App\Entity\IvrServiceLog;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StationController extends AbstractController
{
    /**
     * @IsGranted("ROLE_STATION")
     * @Route("/station", name="station")
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
        return $this->render('station_ivr/index.html.twig', [
            "form" => $form->createView(),
        ]);
    }

    /**
     * @Route("/stationQuestion", name="station_question")
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function stationQuestion(Request $request)
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
                $rows = $this->stationIvr($firstTime, $lastTime);
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
                    $rows = $this->stationIvr(date('Y-m-d H:i:s', $prevDate),
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
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function stationIvr($firstTime,$lastTime)
    {
        $asteriskEm = $this->getDoctrine()->getManager();
        $durakSorgulama = $asteriskEm->getRepository(IvrServiceLog::class);
        $busStopresult =    $durakSorgulama->createQueryBuilder('st')
            ->select('st.createsAt,st.input,st.callId')
            ->Where('st.createsAt BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $firstTime)
            ->setParameter('endDate', $lastTime)
            ->andwhere('st.alias=:alias')
            ->setParameter('alias', 'İbb Menü İett Durak Sorgulama')
            ->getQuery()->getResult();
        $row = [];
        foreach ($busStopresult as $ivrLog) {
            $ivrDial = $asteriskEm->getRepository(IvrLogs::class)
                ->createQueryBuilder('dial')
                ->select("dial.choice")
                ->where("dial.callId=:callId")
                ->setParameter("callId", $ivrLog["callId"])
                ->andWhere("dial.ivrId=:ivrId")
                ->andWhere('dial.dt BETWEEN :startDate AND :endDate')
                ->setParameter('startDate', $firstTime)
                ->setParameter('endDate', $lastTime)
                ->setParameter("ivrId", 20)
                ->getQuery()->getResult();

            if (count($ivrDial) > 0){
                $dial = $ivrDial[0]["choice"];
            }else{
                $dial = " ";
            }

            $callRepository=$asteriskEm->getRepository(Cdr::class)
                ->createQueryBuilder('call')
                ->select("call.src")
                ->where("call.callId=:callId")
                ->setParameter("callId",$ivrLog["callId"])
                ->andWhere('call.calldate BETWEEN :startDate AND :endDate')
                ->setParameter('startDate', $firstTime)
                ->setParameter('endDate', $lastTime)
                ->orderBy('call.calldate', 'DESC')
                ->getQuery()->getResult();

            if (count($callRepository) > 0){
                $call = $callRepository[0]["src"];
            }else{
                $call = " ";
            }


            $stationCode = explode(",", $ivrLog["input"]);
            $row [] = [
                "callID" => $ivrLog["callId"],
                "dateRange" => " ",
                "dateRangeTime" => " ",
                "callPhone" =>$call,
                "stationCode" => $stationCode[0],
                "dial" => $dial,
                "createsAt" => $ivrLog["createsAt"]
            ];
        };

        return $row;

    }

}