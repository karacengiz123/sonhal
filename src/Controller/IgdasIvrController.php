<?php
/**
 * Created by PhpStorm.
 * User: cengi
 * Date: 19.04.2019
 * Time: 15:28
 */

namespace App\Controller;


use App\Asterisk\Entity\Cdr;
use App\Asterisk\Entity\Queues;
use App\Asterisk\Entity\Ivr;
use App\Asterisk\Entity\IvrLogs;
use App\Entity\IvrServiceLog;
use function r\row;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IgdasIvrController extends AbstractController
{
    /**
     * @IsGranted("ROLE_IGDAS_DETAIL_IVR")
     * @Route("/igdasivrrapor" ,name="igdas_ivr")
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
        return $this->render('igdas_ivr/index.html.twig', [
            "form" => $form->createView(),]);
    }

    /**
     * @Route("/igdas-rapor")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function igdas(Request $request)
    {
        $formValidate = $request->request->all();

        $firstTime = date('Y-m-d H:i:s', strtotime($formValidate["form"]["firstDate"] . "00:00:00"));
        $lastTime = date('Y-m-d H:i:s', strtotime($formValidate["form"]["lastDate"] . "23:59:59"));
        $time = $formValidate["form"]["time"];

        if ($formValidate["form"]["firstDate"] == "" && $formValidate["form"]["lastDate"] == "") {
            return new Response("Lütfen bir tarih aralığı seçiniz..");
        } else {
            $arr = [];
            if ($formValidate["form"]["firstDate"] == $formValidate["form"]["lastDate"] && $formValidate["form"]["time"] == 86400){
                $rows = $this->igdasIvr($firstTime, $lastTime);
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
                    $rows = $this->igdasIvr(date('Y-m-d H:i:s', $prevDate),
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
     * @Route("/igdas-ivr-rapor", name="igdas_ivr_rapor")
     * @param $firstTime
     * @param $lastTime
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function igdasIvr($firstTime,$lastTime)
    {

        $asteriskEm = $this->getDoctrine()->getManager();
        $igdass = $asteriskEm->getRepository(IvrLogs::class)
            ->createQueryBuilder('ig')
            ->select('ig.callId')
            ->where('ig.dt BETWEEN :startDate AND :endDate')
            ->andwhere('ig.ivrId=:ivrId')
            ->andWhere('ig.choice=:choice')
            ->setParameters([
                "startDate"=>$firstTime,
                "endDate"=>$lastTime,
                "ivrId" => 2,
                "choice" => 2
            ])
            ->getQuery()->getResult();

        $igdasTotalCall = count($igdass);

        $row = [];
        $igdasTotalAnswerCall = 0;
        foreach ($igdass as $igdas) {
            $activityDeger = $asteriskEm->getRepository(IvrServiceLog::class)
                ->createQueryBuilder('cd')
                ->select('count(cd.id)')
                ->where('cd.callId=:callId')
                ->andWhere('cd.createsAt BETWEEN :startDate AND :endDate')
                ->andWhere('cd.alias=:alias')
                ->setParameters([
                    "callId" => $igdas["callId"],
                    "alias" => "İgdaş Menü Agent Aktivite Oluştur",
                    "startDate"=>$firstTime,
                    "endDate"=>$lastTime
                ])
                ->getQuery()->getSingleScalarResult();

            $igdasTotalAnswerCall = $activityDeger + $igdasTotalAnswerCall;
        }
        $ivrTotalCloseCall = $igdasTotalCall - $igdasTotalAnswerCall;

        $row [] = [
            "dateRange"=> " ",
            "dateRangeTime" => " ",
            "igdasTotalCall" => $igdasTotalCall,
            "ivrTotalCloseCall" => $ivrTotalCloseCall,
            "igdasTotalAnswerCall" => $igdasTotalAnswerCall

        ];

        return $row;
    }
//
}