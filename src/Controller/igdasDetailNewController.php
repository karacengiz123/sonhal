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
use App\Entity\Calls;
use App\Entity\UserProfile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class igdasDetailNewController extends AbstractController
{
    /**
     * @IsGranted("ROLE_IGDAS_DETAIL_NEW")
     * @Route("/igdasssnew", name="igdas_details_new")
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
                    "hidden" => true
                ]
            ])
            ->getForm();
        return $this->render('igdas_detail/index.html.twig', [
            "form" => $form->createView(),]);
    }

    /**
     * @Route("/igdasdetailsnew")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function igdasNew(Request $request)
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
                $rows = $this->igdasDetailNew($firstTime, $lastTime);
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
                    $rows = $this->igdasDetailNew(date('Y-m-d H:i:s', $prevDate),
                        date('Y-m-d H:i:s', $range - 1));
                    if (count($rows) > 0) {
                        foreach ($rows as $row) {
                            $row["dateRange"] = date('Y-m-d', $prevDate);
                            $row["dateRangeTime"] = date('H:i:s', $prevDate) . " - " . date('H:i:s', $range - 1);
                            $arr[] = $row;
                        }
                    }
                    $prevDate = $range;
                }
            }
            return $this->json($arr);
        }

    }

//    /**
//     * @param $firstTime
//     * @param $lastTime
//     * @return array
//     */


    /**
     * @param $firstTime
     * @param $lastTime
     * @return array
     */
    public function igdasDetailNew($firstTime, $lastTime)
    {
        $em = $this->getDoctrine()->getManager();
        $igds = $em->getRepository(Calls::class)
            ->createQueryBuilder('c')
            ->where("c.callStatus=:callStatus")
            ->andWhere("c.callType=:callType")
            ->andWhere("c.dtExten IS NOT NULL")
            ->andWhere("c.dtQueue IS NOT NULL")
            ->andwhere('c.dt BETWEEN :startDate AND :endDate')
            ->setParameter("callStatus", "Done")
            ->setParameter("callType", "Inbound")
            ->setParameter('startDate', $firstTime)
            ->setParameter('endDate', $lastTime)
            ->getQuery()->getResult();

        $row = [];
        foreach ($igds as $igd) {
            $arayan = $igd->getClid();
            $kuyruk = $igd->getQueue();
            $temsilci = $igd->getExten();

            $konusmaSuresi = 0;
            if ($igd->getDtExten() != null) {
                $konusmaSuresi = $igd->getDurExten();

            }
            // kuyruk suresı exten dolu ıse  dt exten - dtqueue
            //else dthangup - dtqueue
            $kuyrukBeklemeSuresi = 0;
            if ($igd->getExten() != null) {
                $kuyrukBeklemeSuresi = $igd->getDurQueue();
            }
//            $ivrSuresi = 0;
//            if ($igd->getDtQueue() == null) {
                $ivrSuresi = $igd->getDurIvr();
//            }
            $aramaZamani = $igd->getDt();

            $aramaZamaniFormat = date_format($aramaZamani, 'd-m-Y H:i:s');
            $convertArm = strtotime($aramaZamaniFormat);
            $totalArm = $convertArm + $ivrSuresi+$kuyrukBeklemeSuresi+$konusmaSuresi +3;
            $aramaBitisZamani = date('d-m-Y H:i:s', $totalArm);


            $kuyrugaGirmeZamani = $igd->getDtQueue();

            $kuyrukGirmeZamaniFormat = date_format($kuyrugaGirmeZamani, 'd-m-Y H:i:s');
            $convertKbz = strtotime($kuyrukGirmeZamaniFormat);
            $totalQueue = $convertKbz + $igd->getDurQueue();
            $kuyrukBitisZamani = date('d-m-Y H:i:s', $totalQueue);


//            $temsilciyeDusmeZamani = $igd->getDtExten();
//
//            $kapanmaZamani = $igd->getDtHangup();
            $row[] = [
                "dateRangeTime" => " ",
                "arayannumara" => $arayan,
                "aranannumara" => "beyazmasa",
                "kuyrukBeklemeSuresi" => $kuyrukBeklemeSuresi,
                "ivrsuresi" => $ivrSuresi,
                "calmasuresi" => 3,
                "konusmasuresi" => $konusmaSuresi,
                "ivrmenusecim" => "IGDAS",
                "kuyruk" => " İGDAŞ",
                "temsilci" => $temsilci,
                "baslangicZamani" => date_format($aramaZamani, 'd-m-Y H:i:s'),
                "bitisZamani" => $aramaBitisZamani,
                "kgiris" => date_format($kuyrugaGirmeZamani, 'd-m-Y H:i:s'),
                "kbitis" => $kuyrukBitisZamani

            ];
        }


        return $row;
    }

}