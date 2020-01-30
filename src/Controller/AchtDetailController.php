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
use App\Asterisk\Entity\Queues;
use App\Asterisk\Repository\QueuesRepository;
use App\Entity\AcwLog;
use App\Entity\Calls;
use App\Entity\HoldLog;
use App\Entity\User;
use App\Entity\UserProfile;
use App\Helpers\Date;
use r\Queries\Aggregations\Count;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AchtDetailController extends AbstractController
{

    private $queueName;

    public function queueName()
    {
        $em = $this->getDoctrine()->getManager();
        $queues = $em->getRepository(Queues::class)->findAll();
        $queueRow = [];
        foreach ($queues as $queue) {
            $queueRow[$queue->getQueue()] = $queue->getDescription();
        }
        $this->queueName = $queueRow;
    }


    /**
     * @IsGranted("ROLE_CALL_DETAIL")
     * @Route("/form-acht-details", name="form_acht_details")
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
                    "1 Aylık Aralıklarla" => 30 * 24 * 60 * 60,
                ],
                "attr" => ["class" => "form-control select2"]

            ])
            ->getForm();

        return $this->render('acht_detail/index.html.twig', [
            "form" => $form->createView(),]);
    }

    /**
     * @Route("/achtdetails")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function acht(Request $request)
    {

//        $this->queueName = $queuesRepository->getQueueAllName($queuesRepository);


        $formValidate = $request->request->all();
        $firstTime = date('Y-m-d H:i:s', strtotime($formValidate["form"]["firstDate"] . "00:00:00"));

        $lastTime = date('Y-m-d H:i:s', strtotime($formValidate["form"]["lastDate"] . "23:59:59"));
        $time = $formValidate["form"]["time"];

        if ($formValidate["form"]["firstDate"] == "" && $formValidate["form"]["lastDate"] == "") {
            return new Response("Lütfen bir tarih aralığı seçiniz..");
        } else {
            $arr = [];
            if ($formValidate["form"]["firstDate"] == $formValidate["form"]["lastDate"] && $formValidate["form"]["time"] == 86400) {
                $rows = $this->achtDetail($firstTime, $lastTime);
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
                    $rows = $this->achtDetail(date('Y-m-d H:i:s', $prevDate), date('Y-m-d H:i:s', $range - 1));
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
    public function achtDetail($firstTime, $lastTime)
    {

        $this->queueName();
        $em = $this->getDoctrine()->getManager();


        $callsRepository = $em->getRepository(Calls::class);
        $calls = $callsRepository->createQueryBuilder("cl")
            ->select("COUNT(cl.idx) as Count,SUM(cl.durExten) as SpeakTime,cl.queue,cl as callItem")
            ->where("cl.dt between :sDate and :eDate")
            ->andWhere("cl.exten IS NOT NULL")
            ->andWhere("cl.callStatus=:callStatus")
            ->andWhere("cl.callType=:callType")
            ->andWhere("cl.queue  IS NOT NULL")
            ->andWhere("cl.dtExten IS NOT NULL")
            ->setParameters([
                "sDate" => $firstTime,
                "eDate" => $lastTime,
                "callStatus" => "Done",
                "callType" => "Inbound",
            ])
            ->groupBy("cl.queue,cl.user")
            ->getQuery()->getResult();


        $row = [];
        foreach ($calls as $call) {

            /**
             * @var User $user
             * @var Calls $callItem
             */
            $callItem = $call["callItem"];
            $user = $callItem->getUser();
//            if (!is_null($user)){
            if (!is_null($user)){
                $holdLogs = $user->getHoldLogs()->toArray();
                $countHold = 0;
                $sumHold = 0;
                if (count($holdLogs) > 0) {
                    /**
                     * @var  HoldLog $holdLog
                     */
                    foreach ($holdLogs as $holdLog) {
                        if ($holdLog->getUniqueId() == $call["callItem"]->getUid2()) {
                            $countHold += 1;
                            $sumHold += $holdLog->getDuration();
                        }
                    }
                }

                $acwLogs = $user->getAcwLogs()->toArray();
                $countAcw = 0;
                $sumAcw = 0;
                if (count($acwLogs) > 0) {
                    /**
                     * @var AcwLog $acwLog
                     */

                    foreach ($acwLogs as $acwLog) {
                        if ($acwLog->getAcwType()->getId() == 1) {
                            if ($acwLog->getCallId() == $call["callItem"]->getUid2()){
                                $countAcw += 1;
                                $sumAcw += $acwLog->getDuration();
                            }
                        }
                    }
                }

                $row[] = [
                    "tckn" => $user->getUserProfile()->getTckn(),
                    "userName" => $user->getUserProfile()->__toString(),
                    "queue" => $this->queueName[$call["queue"]],
                    "callCount" => $call["Count"],
                    "acht" => gmdate("H:i:s", (($call["SpeakTime"] + (($countAcw? $sumAcw / $countAcw : 0) *  $call["Count"]))) / $call["Count"]),
                    "avgACD" => gmdate("H:i:s", 3),
                    "avgACW" => gmdate("H:i:s", $countAcw? $sumAcw / $countAcw : 0),
                    "speakTime" => gmdate("H:i:s", $call["SpeakTime"]),
                    "sumAcw" =>($countAcw? $sumAcw / $countAcw : 0) *  $call["Count"],
                    "sumHold" => $sumHold,
                    "countHold" => $countHold,
                    "avgHold" => gmdate("H:i:s", $countHold ? $sumHold / $countHold : 0),
                ];
            }

        }
//        }
        return $row;
    }
}