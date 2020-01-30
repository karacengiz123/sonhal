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
use App\Entity\Evaluation;
use App\Entity\FormCategory;
use App\Entity\FormTemplate;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\UserProfile;
use App\Helpers\Date;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EvaExternalCountReportController extends AbstractController
{
    /**
     * @IsGranted("ROLE_CALL_DETAIL")
     * @Route("/evaluatorExternalCountReportForm" ,name="evaluator-external-count-report-form")
     * @param Request $request
     * @return Response
     */
    public function form(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $formTemplate = $em->getRepository(FormTemplate::class)->findAll();
        foreach ($formTemplate as $formTemp) {
            $choiceformTemp [$formTemp->getTitle()] = $formTemp->getId();
        }
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
            ->add("formTemplateId", ChoiceType::class, [
                "label" => "Form Seçiniz",
                'choices' => $choiceformTemp,
                "attr" => ["class" => "form-control select2"],
                'multiple'  => true,
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
        return $this->render('evaluator_external_count_report/index.html.twig', [
            "form" => $form->createView(),]);
    }

    /**
     * @Route("/evaluatorExternalCountReportTime")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
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
            if ($formValidate["form"]["firstDate"] == $formValidate["form"]["lastDate"] && $formValidate["form"]["time"] == 86400) {
                $rows = $this->evaluatorExternalCountReport($firstTime, $lastTime, $formValidate["form"]["formTemplateId"]);
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
                    $rows = $this->evaluatorExternalCountReport(date('Y-m-d H:i:s', $prevDate), date('Y-m-d H:i:s', $range - 1), $formValidate["form"]["formTemplateId"]);
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
     * @param $firsDate
     * @param $lastDate
     * @param $formTemplateId
     * @return array
     */
    public function evaluatorExternalCountReport($firsDate,$lastDate,$formTemplateId)
    {
        $em = $this->getDoctrine()->getManager();

            $users = $em->getRepository(User::class)->findAll();
//            $users = $team->getUsers()->toArray();
            $userId = [];
            /**
             * @var User $user
             */
            foreach ($users as $user) {
                $userId [] = $user->getId();

            }
            $evaluations = $this->getDoctrine()->getRepository(Evaluation::class);


            $evaluatedList = $evaluations->createQueryBuilder('e');
            $evaluatedList
//            ->select("(e.updatedAt) as updatedAt,(e.createdAt) as createdAt,e as evaItem,COUNT(e.id) as count")
                ->select("COUNT(e.id) as count,
            
            AVG(e.score) as AVGScore,
            SUM(e.duration) as SumDuration,
            AVG(e.duration) as AvgDuration,Max(e.score) as MaxScore,
            Min(e.score) as MinScore,
            e as evaItem")
                ->where("e.createdAt BETWEEN :sDate AND :eDate")
                ->setParameter("sDate", $firsDate)
                ->setParameter("eDate", $lastDate)
                ->andWhere(
                    $evaluatedList->expr()->in("e.formTemplate",$formTemplateId)
                )
                ->groupBy('e.evaluative');
            $evaluatedList = $evaluatedList->getQuery()->getResult();


            $row = [];



            foreach ($evaluatedList as $evaList) {


                /**
                 * @var User $user
                 * @var Evaluation $evaListItem
                 */
                $evaListItem = $evaList["evaItem"];
                $user = $evaListItem->getUser();


//            $max2str= ($max1-$max2)/ $evaList["count"];
                $row[] = [
                    "Degerlendiren" => $evaListItem->getEvaluative()->getUserProfile()->__toString(),
                    "evaCount" => $evaList["count"],
                    "AVGScore" => $evaList["AVGScore"],
                    "SumDuration" => gmdate("H:i:s",$evaList["SumDuration"]),
                    "AvgDuration" => gmdate("H:i:s",$evaList["AvgDuration"]),
                    "MinScore" => $evaList["MinScore"],
                    "MaxScore" => $evaList["MaxScore"],
                ];
            }

        return $row;
    }
}