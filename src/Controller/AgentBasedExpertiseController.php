<?php


namespace App\Controller;


use App\Asterisk\Entity\Cdr;
use App\Asterisk\Entity\Queues;
use App\Asterisk\Entity\Ivr;
use App\Asterisk\Entity\IvrLogs;
use App\Entity\Calls;
use App\Entity\UserProfile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AgentBasedExpertiseController extends AbstractController
{

    private $queueName;
    private $userNAme;

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

    public function userName()
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository(UserProfile::class)->findAll();
        $userRow = [];
        foreach ($users as $user) {
            $userRow[$user->getExtension()] = [
                "tc" => $user->getTckn(),
                "username" => $user->getFirstName() . " " . $user->getLastName()
            ];
        }
        $this->userNAme = $userRow;
    }


    /**
     * @IsGranted("ROLE_AGENT_BASED_EXPERTISE")
     * @Route("/agentbasedexpertise",name="agent_based_expertise")
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
        return $this->render('agent_based_expertise/index.html.twig', [
            "form" => $form->createView(),]);
    }

    /**
     * @Route("/BaseExpertise")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function base(Request $request)
    {
        $this->queueName();
        $this->userNAme();

        $formValidate = $request->request->all();
        $firstTime = date('Y-m-d H:i:s', strtotime($formValidate["form"]["firstDate"] . "00:00:00"));
        $lastTime = date('Y-m-d H:i:s', strtotime($formValidate["form"]["lastDate"] . "23:59:59"));
        $time = $formValidate["form"]["time"];

        if ($formValidate["form"]["firstDate"] == "" && $formValidate["form"]["lastDate"] == "") {
            return new Response("Lütfen bir tarih aralığı seçiniz..");
        } else {
            $arr = [];
            $columns = [];
            if ($formValidate["form"]["firstDate"] == $formValidate["form"]["lastDate"] && $formValidate["form"]["time"] == 86400) {
                list($rows, $columns) = $this->baseExpertise($firstTime, $lastTime);
                if (is_array($rows)) {
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
                foreach ($ranges as $range) {
                    if ($prevDate == null) {
                        $prevDate = $range;
                        continue;
                    }
                    list($rows, $columns) = $this->baseExpertise(date('Y-m-d H:i:s', $prevDate),
                        date('Y-m-d H:i:s', $range - 1));

                    if (is_array($rows)) {
                        foreach ($rows as $row) {
                            $row["dateRange"] = date('Y-m-d', $range - 86400);
                            $row["dateRangeTime"] = date('H:i:s', $prevDate) . " - " . date('H:i:s', $range - 1);
                            $arr[] = $row;
                        }

                    $prevDate = $range;
                    }
                }
            }
        }
        return $this->json(["row" => $arr, "column" => $columns[0]]);
    }


//    /**
//     * @Route("/asdasddsdasdad")
//     * @return JsonResponse
//     */
//    public function test()
//    {
//
//        $em = $this->getDoctrine()->getManager();
//
//        $calls = $em->getRepository(Calls::class)->createQueryBuilder("c");
//        $calls
//            ->select("c.exten,c.queue,count(c.idx) as count")
//            ->where("c.dt BETWEEN :start AND :end")
//            ->andWhere("c.dtQueue IS NOT NULL")
//            ->andWhere("c.dtExten IS NOT NULL")
//            ->andWhere("c.dtHangup IS NOT NULL")
////            ->andWhere("c.user IS NOT NULL")
//            ->andWhere("c.callStatus=:callStatus")
//            ->setParameters([
//                "start" => "2019-07-02 00:00:00",
//                "end" => "2019-07-03 23:59:59",
//                "callStatus" => "Done"
//            ])
//            ->groupBy("c.queue,c.user");
//        $calls = $calls->getQuery()->getArrayResult();
//
//        if ($calls != null) {
//            print_r("asdada");
//        }
//        return $this->json($calls);
//
//
//    }


    /**
     * @param $firstTime
     * @param $lastTime
     * @return array
     */
    public function baseExpertise($firstTime, $lastTime)
    {

        $em = $this->getDoctrine()->getManager();

        $calls = $em->getRepository(Calls::class)->createQueryBuilder("c");
        $calls
            ->select("c.exten,c.queue,count(c.idx) as count")
            ->where("c.dt BETWEEN :start AND :end")
            ->andWhere("c.dtQueue IS NOT NULL")
            ->andWhere("c.dtExten IS NOT NULL")
            ->andWhere("c.dtHangup IS NOT NULL")
//            ->andWhere("c.user IS NOT NULL")
            ->andWhere("c.callStatus=:callStatus")
            ->setParameters([
                "start" => $firstTime,
                "end" => $lastTime,
                "callStatus" => "Done"
            ])
            ->groupBy("c.queue,c.user");
        $calls = $calls->getQuery()->getArrayResult();

        $callRows = [];
        $rows = [];
        $columns = [];
        $columnsArr = false;
        $columsCol = [];
        if (count($calls) > 0) {
            foreach ($calls as $call) {
                $callRows [$call["exten"]][$call["queue"]] = $call["count"];
            }

            foreach ($this->userNAme as $userKey => $userValue) {

                foreach ($callRows as $callRowKey => $callRowValue) {
                    if ($userKey == $callRowKey) {
                        $rowColumns = [];
                        $rowColumns ["dateRange"] = " ";
                        $columsCol [] = ["data" => "dateRange", "name" => "dateRange", "title" => "TARİH"];

//                        $rowColumns ["dateRangeTime"] = " ";
//                        $columsCol [] = ["data" => "dateRangeTime", "name" => "dateRangeTime", "title" => "24/15 DAKİKALIK VEYA SASATLİK"];

                        $rowColumns ["agentTc"] = $userValue["tc"];
                        $columsCol [] = ["data" => "agentTc", "name" => "agentTc", "title" => "TC"];

                        $rowColumns ["agent"] = $userValue["username"];
                        $columsCol [] = ["data" => "agent", "name" => "agent", "title" => "TEMSİLCİ"];

                        foreach ($this->queueName as $queueKey => $queueVal) {
                            if (array_key_exists($queueKey, $callRowValue)) {
                                $rowColumns [$queueVal] = $callRowValue[$queueKey];
                            } else {
                                $rowColumns [$queueVal] = 0;
                            }
                            $columsCol [] = ["data" => $queueVal, "name" => $queueVal, "title" => $queueVal];
                        }

                        $rows [] = $rowColumns;
                        $columns [] = $columsCol;
                    }
                }
            }
        } else {
            $rows = null;
            $columns = null;
        }

        return array($rows, $columns);

    }
}