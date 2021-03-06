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
use App\Entity\Calls;
use App\Entity\LoginLog;
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

class LoginReportFirstINLastOutController extends AbstractController
{
    /**
     * @IsGranted("ROLE_CALL_DETAIL")
     * @Route("/log-report-detail-firstLast", name="log_report_detail_firstLast")
     * @param Request $request
     * @return Response
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
        return $this->render('log_report_detail_first_in_last_out/index.html.twig', [
            "form" => $form->createView(),]);
    }

    /**
     * @Route("/logreportsfirstLast")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function login(Request $request)
    {
        $formValidate = $request->request->all();
        $firstTime = date('Y-m-d H:i:s', strtotime($formValidate["form"]["firstDate"] . "00:00:00"));

        $lastTime = date('Y-m-d H:i:s', strtotime($formValidate["form"]["lastDate"] . "23:59:59"));
        $time = $formValidate["form"]["time"];

        if ($formValidate["form"]["firstDate"] == "" && $formValidate["form"]["lastDate"] == "") {
            return new Response("Lütfen bir tarih aralığı seçiniz..");
        } else {
            $arr = [];
            if ($formValidate["form"]["firstDate"] == $formValidate["form"]["lastDate"] && $formValidate["form"]["time"] == 86400) {
                $rows = $this->loginDetailFirstLast($firstTime, $lastTime);
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
                    $rows = $this->loginDetailFirstLast(date('Y-m-d H:i:s', $prevDate), date('Y-m-d H:i:s', $range - 1));
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
    public function loginDetailFirstLast($firstTime, $lastTime)
    {

        $em = $this->getDoctrine()->getManager();
        $logReps = $em->getRepository(LoginLog::class)->createQueryBuilder("l")
            ->select("min(l.StartTime) as FTime,max(l.EndTime) as LTime,u.id as UserID,CONCAT(up.firstName, ' ', up.lastName) AS fullName")
            ->addSelect("up.tckn as TC")
            ->join("l.userId","u")
            ->join("u.userProfile","up")
            ->groupBy("u.id")
            ->where("l.StartTime BETWEEN :sDate AND :eDate")
            ->andWhere("l.EndTime is not null")
            ->setParameters([
                "sDate"=>$firstTime,
                "eDate"=>$lastTime,
            ])
            ->getQuery()->getArrayResult();
        $rows = [];

        foreach ($logReps as $logRep) {
            $tc=$logRep["TC"];
            $userName=$logRep["fullName"];
            $Ftime=$logRep["FTime"];
            $Etime=$logRep["LTime"];
            $rows[] = [
                "dateRangeTime" => " ",
                "Tc"=>$tc,
                "Temsilci"=>$userName,
                "FirstStartTime"=>$Ftime,
                "LastExitTime"=>$Etime,
//                "GirisZamani"=>date_format($startTime," H:i:s " ),
//                "ÇikisZamani"=>date_format($endTime," H:i:s " )
            ];
        }


        return $rows;
    }
}