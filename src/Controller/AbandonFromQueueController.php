<?php
/**
 * Created by PhpStorm.
 * User: cengi
 * Date: 2.05.2019
 * Time: 17:25
 */

namespace App\Controller;


use App\Asterisk\Entity\Cdr;
use App\Asterisk\Entity\QueueLog;
use App\Asterisk\Entity\IvrLogs;
use App\Asterisk\Repository\QueuesRepository;
use App\Entity\Calls;
use App\Entity\User;
use App\Entity\UserProfile;
use App\Helpers\Date;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AbandonFromQueueController extends AbstractController
{

    private $queueName;


    /**
     * @IsGranted("ROLE_CALL_DETAIL")
     * @Route("/abandon-from-details", name="abandon_from_details")
     * @param Request $request
     * @param QueuesRepository $queuesRepository
     * @return Response
     */
    public function form(Request $request, QueuesRepository $queuesRepository)
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

        return $this->render('abandon_from_queue/index.html.twig', [
            "form" => $form->createView(),]);
    }

    /**
     * @Route("/abandonfromdetails")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function igdas(Request $request, QueuesRepository $queuesRepository)
    {

        $this->queueName = $queuesRepository->getQueueAllName($queuesRepository);


        $formValidate = $request->request->all();
        $firstTime = date('Y-m-d H:i:s', strtotime($formValidate["form"]["firstDate"] . "00:00:00"));

        $lastTime = date('Y-m-d H:i:s', strtotime($formValidate["form"]["lastDate"] . "23:59:59"));
        $time = $formValidate["form"]["time"];

        if ($formValidate["form"]["firstDate"] == "" && $formValidate["form"]["lastDate"] == "") {
            return new Response("Lütfen bir tarih aralığı seçiniz..");
        } else {
            $arr = [];
            if ($formValidate["form"]["firstDate"] == $formValidate["form"]["lastDate"] && $formValidate["form"]["time"] == 86400) {
                $rows = $this->abandonFromAbandon($firstTime, $lastTime);
                if (count($rows) > 0) {
                    foreach ($rows as $row) {
                        $row["dateRange"] = date('Y-m-d', strtotime($formValidate["form"]["firstDate"] . "00:00:00"));
                        $row["dateRangeTime"] = date('H:i:s', strtotime($formValidate["form"]["firstDate"] . "00:00:00")) . " - " . date('H:i:s', strtotime($formValidate["form"]["lastDate"] . "23:59:59"));

                        $arr[] = $row;
                    }
                }
            } else {
                $lastTime = date("Y-m-d H:i:s", strtotime($lastTime) + 86400);

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
                    $rows = $this->abandonFromAbandon(date('Y-m-d H:i:s', $prevDate), date('Y-m-d H:i:s', $range - 1));
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

    /**
     * @param $firstTime
     * @param $lastTime
     * @return array
     */
    public function abandonFromAbandon($firstTime,$lastTime)
    {

        $em = $this->getDoctrine()->getManager();
        $callDetails = $em->getRepository(Calls::class)
            ->createQueryBuilder('c')
            ->where("c.callStatus=:callStatus")
            ->andWhere("c.callType=:callType")
            ->andWhere("c.dtQueue IS NOT NULL")
            ->andWhere("c.user IS NULL")
            ->andwhere('c.dt BETWEEN :startDate AND :endDate')
            ->setParameter("callType", "Inbound")
            ->setParameter("callStatus", "Done")
            ->setParameter('startDate', $firstTime)
            ->setParameter('endDate', $lastTime)
            ->getQuery()->getResult();


        $row = [];
        foreach ($callDetails as $callDetail) {

            $aramaZamani = $callDetail->getDt();
            $arayan = $callDetail->getClid();
            $kuyruk = $callDetail->getQueue();
            $kuyrukGiris = $callDetail->getDtQueue();
            $kuyrukSuresi = $callDetail->getDurQueue();

            $kuyrukGirisFormat = date_format($kuyrukGiris, "Y-m-d H:i:s");
            $convertArm = strtotime( $kuyrukGirisFormat);
            $totalArm = $convertArm + $kuyrukSuresi;
            $kuyrukCikisFormat=date('d-m-Y H:i:s', $totalArm);


//            $strKuyruk=strtotime($kuyrukGirisDateFormat)+$kuyrukSuresi;
//            $strDateKuyruk=date_format($strKuyruk,"Y-m-d H:i:s");



//           dump( $kuyrukCikisFormat);
//            exit();

            $ivrSuresi = $callDetail->getdurivr();
            $kapanmaZamani = $callDetail->getDtHangup();

//            $kuyrugaGirmeZamani = $callDetail->getDtQueue();

            $row[] = [
                "dateRangeTime" => " ",
                "aramaZamani" => date_format($aramaZamani, "Y-m-d H:i:s"),
                "arayannumara" => $arayan,
                "kuyruk" => $this->queueName[$kuyruk],
                "kapanmaZamani" => date_format($kapanmaZamani, "Y-m-d H:i:s"),

                "kuyrukGiris" => date_format($kuyrukGiris, "Y-m-d H:i:s"),
                "kuyrukCikis" =>$kuyrukCikisFormat,
                "kuyrukSüresi" => $kuyrukSuresi,
                "ivrSüresi" => $ivrSuresi,

            ];

        }
        return $row;


    }
}