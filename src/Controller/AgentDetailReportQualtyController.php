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
use App\Entity\LogEvaluation;
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
use function Symfony\Component\VarDumper\Dumper\esc;

class AgentDetailReportQualtyController extends AbstractController
{
    /**
     * @IsGranted("ROLE_EVALUATOR_REPORTS")
     * @Route("/agentDetailReportsQualtyForm", name="agent-detail-reports-qualty-form")
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
                "label" => "Saat Aralıkları",
                'choices' => [
                    "1 Günlük Aralıklarla" => 24 * 60 * 60,
//                    "15 Dakikalık Aralıklarla" => 15 * 60,
//                    "30 Dakikalık Aralıklarla" => 30 * 60,
                    "1 Saatlik Aralıklarla" => 60 * 60,
//                    "1 Aylık Aralıklarla" => 30 * 24 * 60 * 60,
                ],
                "attr" => ["class" => "form-control"]

            ])
            ->getForm();
        return $this->render('agent_detail_report_qualty/index.html.twig', [
            "form" => $form->createView(),]);
    }

    /**
     * @Route("/agentDetailReportsQualtyTime")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function evaAgentDetail(Request $request)
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
                $rows = $this->agentDetailReportQualty($firstTime, $lastTime);
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
                    $rows = $this->agentDetailReportQualty(date('Y-m-d H:i:s', $prevDate),
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
     * @param $firstTime
     * @param $endTime
     * @return array
     */
    public function agentDetailReportQualty($firstTime, $endTime)
    {

        $statusArray=[
            1=>"DRAFT",
            2=>"YAYINDA",
            3=>"İTİRAZ",
            1=>"OLUMSUZ",
            1=>"GERİ ÇEVİR",
            1=>"GÜNCELLEME DURUMUNDA",
            1=>"RED",
            1=>"GERİ ÇEVİR",
            1=>"GÜNCELLENDİ",
            1=>"KAYDEDİLDİ",
        ];
//        $rows = "";
        $em = $this->getDoctrine()->getManager();

        $evaluations = $em->getRepository(Evaluation::class)->createQueryBuilder("e")
            ->where("e.createdAt BETWEEN :startDate AND :endDate")
            ->setParameter("startDate", $firstTime)
            ->setParameter("endDate", $endTime)
            ->getQuery()->getResult();
//        $evaluations = $em->getRepository(Evaluation::class)->findBy(["evaluative" => 1]);
        $rows = [];
        if (count($evaluations) > 0) {

            foreach ($evaluations as $evaluation) {

                $evoLogs = $em->getRepository(LogEvaluation::class)->createQueryBuilder("le")
                    ->where("le.objectId=:objectId")
                    ->setParameter("objectId", $evaluation->getId())
                    ->getQuery()->getResult();


//                    exit();
////                $arr[]=$evoLogs[0];
////                $endArrScore[]=end($evoLogs);
////                dump($arr);
////                dump($endArrScore);
////                    exit();
//
////                dump($evoLogs);
////                    if (isset($evoLog->getData()["status"])) {
////                        if ($evoLog->getData()["status"]["id"] == 10 ) {
////
////
//                            $formTemplate = $evaluation->getFormTemplate()->getTitle();
//                            $evaluationSource = $evaluation->getSource()->getName();
//                            $citizenId = $evaluation->getCitizenID();
//
//
//                            $updateDate = $evaluation->getUpdatedAt();
//                            $createdDate = $evaluation->getCreatedAt();
////                            $degerlendiren = $evaluation->getEvaluative()->getUserProfile()->__toString();
//                            /**
//                             * @var LogEvaluation $evoLog
//                             */
//                            $degerlendiren = $arr["username"];
//                            $phoneNumber = $evaluation->getPhoneNumber();
//                            if (is_null($evaluation->getCallDate())) {
//                                $callDate = "";
//                            } else {
//                                $callDate = date_format($evaluation->getCallDate(), "Y-m-d H:i:s");
//                            }
//                            $team = $evaluation->getUser()->getTeamId()->getName();
//                            $agentTckn = $evaluation->getUser()->getUserProfile()->getTckn();
//                            $agentId = $evaluation->getUser()->getUserName();
//
//                            $agent = $evaluation->getUser()->getUserProfile()->__toString();
//
//                if (is_null($evaluation->getResetReason()))
//                {
//                    $evaResult="";
//                }
//                else{
//                    $evaResult =  $evaluation->getResetReason();
//                }
//                            $score = $evaluation->getScore();
//                            $duration = $evaluation->getDuration();
//                            $status = $evaluation->getStatus()->getName();
//                            $note = $evaluation->getComment();
//                            $evaluationReasonType = $evaluation->getEvaluationReasonType()->getName();
//
//                            $evaMaxResult="";
//                            if ($score == 100) {
//                                $evaMaxResult = "MAX";
//                            }
//                                if (is_null($evaluation->getResetReason())) {
//                                    $callDate = "";
//                                }
//                                else {
//                                    $evaMaxResult = $evaluation->getResetReason()->getName();
//                                }
//
//
//                            $rows[] = [
//                                "dateRangeTime" => " ",
//                                "formTemplate" => $formTemplate,
//                                "evaluationSource" => $evaluationSource,
//                                "citizenId" => $citizenId,
//                                "updateDate" => date_format($updateDate, "Y-m-d H:i:s"),
//                                "createdDate" => date_format($createdDate, "Y-m-d H:i:s"),
//                                "degerlendiren" => $degerlendiren,
////                    "evaluative" => $evaluative,
//                                "agent" => $agent,
//                                "team" => $team,
//                                "agentTckn" => $agentTckn,
//                                "agentId" => $agentId,
//                                "phoneNumber" => $phoneNumber,
//                                "callDate" => $callDate,
//                                "duration" => $duration,
//                                "status" => $status,
//                                "score" => $score,
//                                "note" => $note,
//                                "evaluationReasonType" => $evaluationReasonType,
//                                "evaMaxResult" => $evaMaxResult,
//
//
//                            ];
//

                $firstData=$evoLogs[0];
                $endData=end($evoLogs);
                $formTemplate=$evaluation->getFormTemplate()->getTitle();
                $evaluationSource=$evaluation->getSource()->getName();
                $citizenId=$evaluation->getCitizenID();

                $updateDate=$evaluation->getUpdatedAt();
                $createdDate=$evaluation->getCreatedAt();

                $degerlendiren=$firstData->getUserName();
                $phoneNumber=$evaluation->getPhoneNumber();

                $callDate=date_format($evaluation->getCallDate(),"Y-m-d H:i:s");

                $team=$evaluation->getUser()->getTeamId()->getName();
                $agentTckn=$evaluation->getUser()->getUserProfile()->getTckn();
                $agentId=$evaluation->getUser()->getUserName();

                $agent=$evaluation->getUser()->getUserProfile()->__toString();


                if (empty($endData->getData()['score']))
                {
                    $endScore="";
                }
                else{
                    $endScore=$endData->getData()['score'];

                }
                $duration=$evaluation->getDuration();
                $status=$evaluation->getStatus()->getName();
                $note=$evaluation->getComment();
//                $evaluationReasonType=$evaluation->getEvaluationReasonType()->getName();


                if ($endScore==100)
                {
                    $evaMaxResult="MAX";
                }
                else{
                    $evaMaxResult="";
                }

                if (is_null($evaluation->getEvaluationReasonType()))
                {
                    $evaluationReasonType="";
                }
                else
                {
                    $evaluationReasonType=$evaluation->getEvaluationReasonType()->getName();
                }

                $rows[]=[
                    "dateRangeTime"=> " ",
                    "formTemplate"=> $formTemplate,
                    "evaluationSource"=>$evaluationSource,
                    "citizenId"=>$citizenId,
                    "updateDate"=>date_format($updateDate, "Y-m-d H:i:s"),
                    "createdDate"=>date_format($createdDate, "Y-m-d H:i:s"),
                    "degerlendiren"=>$degerlendiren,
//                        "evaluative"=>e$valuative,
                    "agent"=>$agent,
                    "team"=>$team,
                    "agentTckn"=>$agentTckn,
                    "agentId"=>$agentId,
                    "phoneNumber"=>$phoneNumber,
                    "callDate"=>$callDate,
                    "duration"=>$duration,
                    "status"=>$status,
                    "score"=>$endScore,
                    "note"=>$note,
                    "evaluationReasonType"=>$evaluationReasonType,
                    "evaMaxResult"=>$evaMaxResult
                ];
            }
        }
        return $rows;

    }
}