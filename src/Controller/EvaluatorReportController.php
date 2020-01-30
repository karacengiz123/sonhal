<?php


namespace App\Controller;


use App\Asterisk\Entity\Cdr;
use App\Asterisk\Entity\IvrLogs;
use App\Entity\Calls;
use App\Entity\Evaluation;
use App\Entity\FormCategory;
use App\Entity\FormQuestion;
use App\Entity\FormQuestionOption;
use App\Entity\FormSection;
use App\Entity\FormTemplate;
use App\Entity\Team;
use App\Entity\UserProfile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EvaluatorReportController extends AbstractController
{
    /**
     * @IsGranted("ROLE_EVALUATOR_REPORTS")
     * @Route("/evaluatorReportsForm", name="evaluator_reports")
     */
    public function form(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add("firstDate", DateType::class, [
                "attr" => ["class" => "form-control"],

                "label" => "İlk Tarih",
                'widget' => 'single_text',

            ])
            ->add("lastDate", DateType::class, [
                "attr" => ["class" => "form-control"],
                "label" => "Son Tarih",
                'widget' => 'single_text',

            ])
            ->add("time", ChoiceType::class, [
//                "label" => "Saat Aralıkları",
                'choices' => [
                    "1 Günlük Aralıklarla" => 24 * 60 * 60,
//                    "15 Dakikalık Aralıklarla" => 15 * 60,
//                    "30 Dakikalık Aralıklarla" => 30 * 60,
//                    "1 Saatlik Aralıklarla" => 60 * 60,
//                    "1 Aylık Aralıklarla" => 30 * 24 * 60 * 60,
                ],
                "attr" => ["class" => "form-control",
                "hidden"=>true]

            ])
            ->getForm();
        return $this->render('evaluator_report/index.html.twig', [
            "form" => $form->createView(),]);
    }

    /**
     * @Route("/evaluatorReportsTime")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function eva(Request $request)
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
                $rows = $this->evaluatorReport($firstTime, $lastTime);
                if (count($rows) > 0) {
                    foreach ($rows as $row) {
                        $row["dateRange"] = date('Y-m-d', strtotime($formValidate["form"]["firstDate"] . "00:00:00"));
                        $row["dateRangeTime"] = date('H:i:s', strtotime($formValidate["form"]["firstDate"] . "00:00:00")) . " - " . date('H:i:s', strtotime($formValidate["form"]["lastDate"] . "23:59:59"));

                        $arr[] = $row;
                    }
                }
            } else {
//                $lastTime=date("Y-m-d H:i:s",strtotime($lastTime)+86400);

                $ranges = range(
                    strtotime($firstTime),
                    strtotime($lastTime),
                    $time
                );


                $prevDate = null;
                foreach ($ranges as $range) {
                    if ($prevDate == null) {
                        $prevDate = $range;
                        continue;
                    }
                    $rows = $this->evaluatorReport(date('Y-m-d H:i:s', $prevDate),
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


    /**
     * @Route("/evaluatorreport",name="evaluator_report")
     * @param $firstTime
     * @param $endTime
     * @return array
     */
    public function evaluatorReport($firstTime, $endTime)
    {
//        $rows = "";
        $em = $this->getDoctrine()->getManager();

        $evaluations = $em->getRepository(Evaluation::class)->createQueryBuilder("e")
            ->where("e.createdAt BETWEEN :startDate AND :endDate")
            ->andWhere("e.evaluationReasonType !=:evaluationReasonType")
            ->setParameter("startDate", $firstTime)
            ->setParameter("endDate", $endTime)
            ->setParameter("evaluationReasonType", 5)
            ->getQuery()->getResult();
//        $evaluations = $em->getRepository(Evaluation::class)->findBy(["evaluative" => 1]);
        $rows = [];
        if (count($evaluations) > 0) {

            foreach ($evaluations as $evaluation) {
                $evaluationSource = $evaluation->getSource()->getName();
                $evaluationReasonType = $evaluation->getEvaluationReasonType()->getName();
                $formTemplate = $evaluation->getFormTemplate()->getTitle();

                $updateDate = $evaluation->getUpdatedAt();
                $createdDate = $evaluation->getCreatedAt();
                //burası yanlış mı dıye sor
                $evaluative =$evaluation->getEvaluative()->getUserProfile()->__toString();
                $agent = $evaluation->getUser()->getUserProfile()->__toString();

                $team = $evaluation->getUser()->getTeamId()->getName();

                $phoneNumber = $evaluation->getPhoneNumber();
                if (is_null($evaluation->getCallDate())) {
                    $callDate = "";
                } else {
                    $callDate = date_format($evaluation->getCallDate(), "Y-m-d H:i:s");
                }

                if (is_null($evaluation->getResetReason())) {
                    $evaResult = " ";
                } else {
                    $evaResult = $evaluation->getResetReason()->getName();
                }
                $duration = $evaluation->getDuration();
                $score = $evaluation->getScore();
                $note = $evaluation->getComment();
                if ($score == 100) {
                    $evaMaxResult = "MAX";
                }
                if(is_null($evaluation->getResetReason())) {
                    $evaMaxResult = " ";
                }else{
                    $evaMaxResult=$evaluation->getResetReason()->getName();
                }


//                dump($score);
//                exit();

                $rows[] = [
                    "dateRangeTime" => " ",
                    "formTemplate" => $formTemplate,
//                    "userFullName" => $userFullName,
                    "evaluationSource" => $evaluationSource,
                    "evaluationReasonType" => $evaluationReasonType,
                    "updateDate" => date_format($updateDate, "Y-m-d H:i:s"),
                    "createdDate" => date_format($createdDate, "Y-m-d H:i:s"),
                    "evaluative" => $evaluative,
                    "agent" => $agent,
                    "team" => $team,
                    "phoneNumber" => $phoneNumber,
                    "callDate" => $callDate,
                    "result" => $evaResult,
                    "duration" => $duration,
                    "score" => $score,
                    "note" => $note,
                    "evaMaxResult" => $evaMaxResult,


                ];

            }
        }
        return $rows;
//
//        return $this->Json($rows);
//        dump($rows);
//        exit();


    }
}