<?php
/**
 * Created by PhpStorm.
 * User: cengi
 * Date: 5.05.2019
 * Time: 19:39
 */

namespace App\Controller;


use App\Asterisk\Entity\Cdr;
use App\Asterisk\Entity\IvrLogs;
use App\Entity\AcwLog;
use App\Entity\AcwType;
use App\Entity\AgentBreak;
use App\Entity\BreakType;
use App\Entity\UserProfile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class AgentDetail extends AbstractController
{



    /**
     * @IsGranted("ROLE_AGENT_DETAIL")
     * @Route("/agentdetail",name="agent_detail")
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
        return $this->render('agent_detail/index.html.twig', [
            "form" => $form->createView(),]);
    }

    /**
     * @Route("/agentdetails")
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
            if ($formValidate["form"]["firstDate"] == $formValidate["form"]["lastDate"] && $formValidate["form"]["time"] == 86400){
                $rows = $this->agentDetail($firstTime, $lastTime);
                if (count($rows) > 0) {
                    foreach ($rows as $row) {
                        $row["dateRange"] = date('Y-m-d', strtotime($formValidate["form"]["firstDate"] . "00:00:00"));
                        $row["dateRangeTime"] = date('H:i:s', strtotime($formValidate["form"]["firstDate"] . "00:00:00")) . " - " . date('H:i:s', strtotime($formValidate["form"]["lastDate"] . "23:59:59"));

                        $arr[] = $row;
                    }
                }
            }else{
                $lastTime=date("Y-m-d H:i:s",strtotime($lastTime)+86400);

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
                    $rows = $this->agentDetail(date('Y-m-d H:i:s', $prevDate),
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
    public function agentDetail($firstTime,$lastTime)
    {
        $em = $this->getDoctrine()->getManager();

        $agentBreaks = $em->getRepository(AgentBreak::class)->createQueryBuilder("ab")
            ->where("ab.startTime BETWEEN :firsDate AND :lastDate")
            ->andWhere("ab.endTime IS NOT NULL")
            ->setParameters([
                "firsDate"=>$firstTime,
                "lastDate"=>$lastTime
            ])->getQuery()->getResult();

        $acwLogs = $em->getRepository(AcwLog::class)->createQueryBuilder("al")
            ->where("al.startTime BETWEEN :firsDate AND :lastDate")
            ->andWhere("al.endTime IS NOT NULL")
            ->setParameters([
                "firsDate"=>$firstTime,
                "lastDate"=>$lastTime
            ])->getQuery()->getResult();

        $row = [];

        foreach ($agentBreaks as $agentBreak){
            $row[] = [
                "dateRange" => " ",
                "dateRangeTime" => " ",
                "agentTc" => $agentBreak->getUser()->getUserProfile()->getTckn(),
                "agent" => $agentBreak->getUser()->getUserProfile()->getFirstName()." ".$agentBreak->getUser()->getUserProfile()->getLastName(),
                "type"=> $agentBreak->getBreakType()->getName(),
                "startTime"=>$agentBreak->getStartTime()->format("d-m-Y H:i:s"),
                "endTime"=>$agentBreak->getEndTime()->format("d-m-Y H:i:s"),
                "diff"=>$agentBreak->getDuration()
            ];
        }


        foreach ($acwLogs as $acwLog){
            $acwType = "";
            if (is_null($acwLog->getAcwType())){
                $acwType = "ACW";
            }else{
                $acwType = $acwLog->getAcwType()->getName();
            }
            $row[] = [
                "dateRange" => " ",
                "dateRangeTime" => " ",
                "agentTc" => $acwLog->getUser()->getUserProfile()->getTckn(),
                "agent" => $acwLog->getUser()->getUserProfile()->getFirstName()." ".$acwLog->getUser()->getUserProfile()->getLastName(),
                "type"=> $acwType,
                "startTime"=>$acwLog->getStartTime()->format("d-m-Y H:i:s"),
                "endTime"=>$acwLog->getEndTime()->format("d-m-Y H:i:s"),
                "diff"=>$acwLog->getDuration()
            ];
        }







        return $row;
//        return $this->json($row);
    }
}