<?php
/**
 * Created by PhpStorm.
 * User: cengi
 * Date: 26.04.2019
 * Time: 17:13
 */

namespace App\Controller;


use App\Entity\AcwLog;
use App\Entity\AcwType;
use App\Entity\AgentBreak;
use App\Entity\BreakType;
use App\Entity\Calls;
use App\Entity\HoldLog;
use App\Entity\LoginLog;
use App\Entity\UserProfile;
use App\Helpers\Date;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AgentTableController extends AbstractController
{
    private $columnsControl;

    public function __construct()
    {
        $this->columnsControl = false;
    }

    /**
     * @IsGranted("ROLE_AGENT_TABLE")
     * @Route("/agent", name="agent_table")
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
        return $this->render('agent_table/index.html.twig', [
            "form" => $form->createView(),
        ]);
    }

    /**
     * @Route("/agenttablereport")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     * @throws \Exception
     */
    public function agent(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $usps = $em->getRepository(UserProfile::class)->findAll();

        $formValidate = $request->request->all();
        $firstTime = date('Y-m-d H:i:s', strtotime($formValidate["form"]["firstDate"] . "00:00:00"));
        $lastTime = date('Y-m-d H:i:s', strtotime($formValidate["form"]["lastDate"] . "23:59:59"));
        $time = $formValidate["form"]["time"];

//        $firstTime = date('Y-m-d H:i:s', strtotime("2019-07-11 " . "00:00:00"));
//        $lastTime = date('Y-m-d H:i:s', strtotime("2019-07-11 " . "23:59:59"));
//        $time = 86400;

        if ($formValidate["form"]["firstDate"] == "" && $formValidate["form"]["lastDate"] == "") {
            return new Response("Lütfen bir tarih aralığı seçiniz..");
        } else {
            $arr = [];
            $column = null;
            if ($formValidate["form"]["firstDate"] == $formValidate["form"]["lastDate"] && $formValidate["form"]["time"] == 86400) {
                foreach ($usps as $usp) {
                    list($rows, $columns) = $this->agentTable($usp, $firstTime, $lastTime);
                    if ($column == null) {
                        $column = $columns;
                    }
                    if (is_array($rows)) {
                        $rows["dateRange"] = date('Y-m-d', strtotime($formValidate["form"]["firstDate"] . "00:00:00"));
                        $rows["dateRangeTime"] = date('H:i:s', strtotime($formValidate["form"]["firstDate"] . "00:00:00")) . " - " . date('H:i:s', strtotime($formValidate["form"]["lastDate"] . "23:59:59"));
                        $rows["agentTc"] = $usp->getTckn();
                        $rows["agent"] = $usp->getFirstName() . " " . $usp->getLastName();
                        $arr[] = $rows;
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
                    foreach ($usps as $usp) {
                        list($rows, $columns) = $this->agentTable($usp, date('Y-m-d H:i:s', $prevDate),
                            date('Y-m-d H:i:s', $range - 1));
                        if ($column == null) {
                            $column = $columns;
                        }
                        if (is_array($rows)) {
                            $rows["dateRange"] = date('Y-m-d', $range - 86400);
                            $rows["dateRangeTime"] = date('H:i:s', $prevDate) . " - " . date('H:i:s', $range - 1);
                            $rows["agentTc"] = $usp->getTckn();
                            $rows["agent"] = $usp->getFirstName() . " " . $usp->getLastName();
                            $arr[] = $rows;
                        }
                    }

                    $prevDate = $range;
                }
            }
        }
        return $this->json(["row" => $arr, "column" => $column]);
    }

//    /**
//     * @Route("/ebene-atlim")
//     * @throws \Doctrine\ORM\NonUniqueResultException
//     */
//    public function testPubFuc()
//    {
//        $em = $this->getDoctrine()->getManager();
//        $usp = $em->getRepository(UserProfile::class)->find(15);
//        dump($usp);
//        list($rows, $columns) = $this->agentTable($usp, "2019-07-10 00:00:00", "2019-07-10 23:59:59");
//        dump($rows);
//        list($rows, $columns) = $this->agentTable($usp, "2019-07-11 00:00:00", "2019-07-11 23:59:59");
//        dump($rows);
//        list($rows, $columns) = $this->agentTable($usp, "2019-07-12 00:00:00", "2019-07-12 23:59:59");
//        dump($rows);
//        list($rows, $columns) = $this->agentTable($usp, "2019-07-13 00:00:00", "2019-07-13 23:59:59");
//        dump($rows);
//        exit();
//    }

    /**
     * @param $usp
     * @param $firstTime
     * @param $lastTime
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function agentTable($usp, $firstTime, $lastTime)
    {

        $em = $this->getDoctrine()->getManager();
        $breakTypesRepository = $em->getRepository(BreakType::class);
        $agentBreakRepository = $em->getRepository(AgentBreak::class);
        $acwTypesRepository = $em->getRepository(AcwType::class);
        $acwLogsRepository = $em->getRepository(AcwLog::class);
        $callsRepository = $em->getRepository(Calls::class);
        $holdLogRepository = $em->getRepository(HoldLog::class);
        $acwTypes = $acwTypesRepository->findAll();
        $breakTypes = $breakTypesRepository->findAll();

        $row = [];
        $row ["dateRange"] = "";
        $row ["dateRangeTime"] = "";
        $row ["agentTc"] = "";
        $row ["agent"] = "";
        $columns = [];
        if ($this->columnsControl == false) {
            $columns [] = ["data" => "dateRange", "name" => "dateRange", "title" => "TARİH"];
            $columns [] = ["data" => "dateRangeTime", "name" => "dateRangeTime", "title" => "24/15 <br> DAKİKALIK <br> VEYA SAATLİK"];
            $columns [] = ["data" => "agentTc", "name" => "agentTc", "title" => "TC NO"];
            $columns [] = ["data" => "agent", "name" => "agent", "title" => "PERSONEL"];
        }


        $totalBreak = 0;
        foreach ($breakTypes as $breakType) {
            $breakSum = $agentBreakRepository->createQueryBuilder("ab")
                ->select("SUM(ab.duration)")
                ->where("ab.user=:user")
                ->andWhere("ab.breakType=:breakType")
                ->andWhere("ab.startTime BETWEEN :startDate AND :endDate")
                ->setParameters([
                    "user" => $usp->getUser(),
                    "startDate" => $firstTime,
                    "endDate" => $lastTime,
                    "breakType" => $breakType
                ])->getQuery()->getSingleScalarResult();

            if ($breakSum == null) {
                $breakSum = 0;
            } else {
                $breakSum = $breakSum + 0;
            }

            $row [$breakType->getName()] = gmdate("H:i:s", $breakSum);
            if ($this->columnsControl == false) {
                $columns [] = ["data" => $breakType->getName(), "name" => $breakType->getName(), "title" => $breakType->getName()];
            }

            $totalBreak += $breakSum;
        }

        $row ["totalBreak"] = gmdate("H:i:s", $totalBreak);
        if ($this->columnsControl == false) {
            $columns [] = ["data" => "totalBreak", "name" => "totalBreak", "title" => "TOPLAM MOLA SÜRESİ"];
        }



        $totalACW = 0;

        foreach ($acwTypes as $acwType) {
            $acwSum = $acwLogsRepository->createQueryBuilder("al")
                ->select("SUM(al.duration)")
                ->where("al.user=:user")
                ->andWhere("al.acwType=:acwType")
                ->andWhere("al.startTime BETWEEN :startDate AND :endDate")
                ->setParameters([
                    "user" => $usp->getUser(),
                    "startDate" => $firstTime,
                    "endDate" => $lastTime,
                    "acwType" => $acwType
                ])->getQuery()->getSingleScalarResult();

            if ($acwSum == null) {
                $acwSum = 0;
            } else {
                $acwSum = $acwSum + 0;
            }

            $row [$acwType->getName()] = gmdate("H:i:s", $acwSum);
            if ($this->columnsControl == false) {
                $columns [] = ["data" => $acwType->getName(), "name" => $acwType->getName(), "title" => $acwType->getName()];
            }
            $totalACW += $acwSum;


        }
        $row ["totalAcw"] = gmdate("H:i:s", $totalACW);
        if ($this->columnsControl == false) {
            $columns [] = ["data" => "totalAcw", "name" => "totalAcw", "title" => "TOPLAM İŞLEM  SÜRESİ"];
        }


        foreach ($acwTypes as $acwType) {
            $acwSum2 = $acwLogsRepository->createQueryBuilder("al")
                ->select("SUM(al.duration)")
                ->where("al.user=:user")
                ->andWhere("al.acwType=:acwType")
                ->andWhere("al.startTime BETWEEN :startDate AND :endDate")
                ->setParameters([
                    "user" => $usp->getUser(),
                    "startDate" => $firstTime,
                    "endDate" => $lastTime,
                    "acwType" => 1
                ])->getQuery()->getSingleScalarResult();

            if ($acwSum2 == null) {
                $acwSum2 = 0;
            } else {
                $acwSum2 = $acwSum2 + 0;
            }

$acwSummm=gmdate("H:i:s", $acwSum2);
            $row["justAcw"] =$acwSummm;

        }

        $loginLogs = $em->getRepository(LoginLog::class)->createQueryBuilder("ll")
            ->where("ll.userId=:user")
            ->andWhere("ll.StartTime BETWEEN :startDate AND :endDate")
            ->setParameters([
                "user" => $usp->getUser(),
                "startDate" => $firstTime,
                "endDate" => $lastTime,
            ])->getQuery()->getResult();

        $loginTime = 0;
        $startlogin = false;
        $startloginTime = new \DateTime();
        $endloginTime = new \DateTime();
        foreach ($loginLogs as $loginLog) {
            if ($startlogin == false) {
                $startloginTime->setTimestamp($loginLog->getStartTime()->getTimestamp());
                $startlogin = true;
            }
            if ($loginLog->getEndTime() == null) {
                $newdate = new \DateTime();
                $endloginTime->setTimestamp($newdate->getTimestamp());
            } else {
                $endloginTime->setTimestamp($loginLog->getEndTime()->getTimestamp());
            }
        }

        $difflogin = Date::diffDateTimeToSecond($endloginTime, $startloginTime);
        $row ["loginTimeAll"] = gmdate("H:i:s", $difflogin);
        if ($this->columnsControl == false) {
            $columns [] = ["data" => "loginTimeAll", "name" => "loginTimeAll", "title" => "LOGİN İLK GİRİŞ SON ÇIKIŞ TOPLAM SÜRE"];
        }

        $loginLogs = $em->getRepository(LoginLog::class)->createQueryBuilder("ll")
            ->where("ll.userId=:user")
            ->andWhere("ll.StartTime BETWEEN :startDate AND :endDate")
            ->setParameters([
                "user" => $usp->getUser(),
                "startDate" => $firstTime,
                "endDate" => $lastTime,
            ])->getQuery()->getResult();

        $loginTime = 0;
        foreach ($loginLogs as $loginLog) {
            if ($loginLog->getEndTime() == null) {
                $loginTime += Date::diffDateTimeToSecond(new \DateTime(), $loginLog->getStartTime());
            } else {
                $loginTime += Date::diffDateTimeToSecond($loginLog->getEndTime(), $loginLog->getStartTime());
            }
        }

        $row ["loginTime"] = gmdate("H:i:s", $loginTime);
        if ($this->columnsControl == false) {
            $columns [] = ["data" => "loginTime", "name" => "loginTime", "title" => "TOPLAM LOGİN SÜRESİ"];
        }


        $callsEnterque = $callsRepository->createQueryBuilder('c')
//            ->select("c as callItem")
            ->Where('c.dt BETWEEN :startDate AND :endDate')
            ->andWhere('c.exten=:exten')
            ->andWhere('c.callStatus=:callStatus')
            ->andWhere("c.callType=:ctype")
            ->setParameters([
                "startDate" => $firstTime,
                "endDate" => $lastTime,
                "exten" => $usp->getExtension(),
                "callStatus" => "Done",
                "ctype" => "Inbound"
            ])
            ->getQuery()->getResult();

        $row ["callsEnterque"] = count($callsEnterque);
        if ($this->columnsControl == false) {
            $columns [] = ["data" => "callsEnterque", "name" => "callsEnterque", "title" => "TOPLAM GELEN ÇAĞRI"];
        }

        $totaldiffCallTime = 0;
        foreach ($callsEnterque as $callTime) {
            if ($callTime->getDtHangUp() == null) {
                $diffCallTime = Date::diffDateTimeToSecond(new \DateTime(), $callTime->getDtExten());
            } else {
//                $diffCallTime = $callTime->getDtHangUp()->getTimeStamp() - $callTime->getDtExten()->getTimeStamp();
                $diffCallTime = $callTime->getDurExten();
            }
            $totaldiffCallTime += $diffCallTime;
        }
        $totalCallTimeResult = gmdate("H:i:s", $totaldiffCallTime);
        $row ["totalCallTime"] = $totalCallTimeResult;
        if ($this->columnsControl == false) {
            $columns [] = ["data" => "totalCallTime", "name" => "totalCallTime", "title" => "Toplam Çağrı Süresi"];
        }
//foreach ($callsEnterque as $forCallInnboundHold)
//{
//    /**
//     * @var  HoldLog $ınboundHold
//     */
//    $ınboundHold=$holdLogRepository->findBy(["uniqueId"=>$forCallInnboundHold->])
//}


        $holdLogs = $holdLogRepository->createQueryBuilder("hl")
            ->where("hl.user=:user")
            ->andWhere("hl.startTime BETWEEN :startDate AND :endDate")
            ->setParameters([
                "user" => $usp->getUser(),
                "startDate" => $firstTime,
                "endDate" => $lastTime,
            ])
            ->getQuery()->getResult();

        $totaldiffHoldTime = 0;
        foreach ($holdLogs as $holdLog) {
            if ($holdLog->getEndTime() == null) {
                $diffHoldTime = Date::diffDateTimeToSecond(new \DateTime(), $holdLog->getStartTime());
            } else {
                $diffHoldTime = Date::diffDateTimeToSecond($holdLog->getEndTime(), $holdLog->getStartTime());
            }
            $totaldiffHoldTime += $diffHoldTime;
        }
        $totalHoldTimeResult = gmdate("H:i:s", $totaldiffHoldTime);
        $row ["totalHoldTime"] = $totalHoldTimeResult;
        if ($this->columnsControl == false) {
            $columns [] = ["data" => "totalHoldTime", "name" => "totalHoldTime", "title" => "TOTAL HOLD"];
        }







////////////////ACHT

        $row ["acht"] = gmdate("H:i:s", ($row ["callsEnterque"] ? ($totaldiffCallTime + $totaldiffHoldTime+$acwSum2) / $row ["callsEnterque"] : 0));
        if ($this->columnsControl == false) {
            $columns [] = ["data" => "acht", "name" => "acht", "title" => "ACHT"];
        }
///////////Kullanması Gereken Mola+Yemek Toplamı1
//        $row ["breakAndEat"] = gmdate("H:i:s", ($loginTime * 15 / 100));
//        if ($this->columnsControl == false) {
//            $columns [] = ["data" => "breakAndEat", "name" => "breakAndEat", "title" => "Kullanması Gereken Mola+Yemek Toplamı"];
//        }
/////////////Kullanması Gereken Mola+Yemek Toplamı2
//        $row ["breakAndEat2"] = gmdate("H:i:s", ($loginTime * 14.81 / 100));
//        if ($this->columnsControl == false) {
//            $columns [] = ["data" => "breakAndEat2", "name" => "breakAndEat2", "title" => "Kullanması Gereken Mola+Yemek Toplamı"];
//        }
//
/////////////Kullanması Gereken Mola+Yemek Toplamı3
//        $row ["breakAndEat3"] = gmdate("H:i:s", ($loginTime * 16.65 / 100));
//        if ($this->columnsControl == false) {
//            $columns [] = ["data" => "breakAndEat3", "name" => "breakAndEat3", "title" => "Kullanması Gereken Mola+Yemek Toplamı"];
//        }
/////////////Kullanması Gereken Mola+Yemek Toplamı4
//        $row ["breakAndEat4"] = gmdate("H:i:s", ($loginTime * 17.65 / 100));
//        if ($this->columnsControl == false) {
//            $columns [] = ["data" => "breakAndEat4", "name" => "breakAndEat4", "title" => "Kullanması Gereken Mola+Yemek Toplamı"];
//        }
//
/////////////Kullanması Gereken Mola+Yemek Toplamı5
//        $row ["breakAndEat5"] = gmdate("H:i:s", ($loginTime * 18.35 / 100));
//        if ($this->columnsControl == false) {
//            $columns [] = ["data" => "breakAndEat5", "name" => "breakAndEat5", "title" => "Kullanması Gereken Mola+Yemek Toplamı"];
//        }
//
//
/////////////Kullanması Gereken Mola+Yemek Toplamı6
//        $row ["breakAndEat6"] = gmdate("H:i:s", ($loginTime * 18.95 / 100));
//        if ($this->columnsControl == false) {
//            $columns [] = ["data" => "breakAndEat6", "name" => "breakAndEat6", "title" => "Kullanması Gereken Mola+Yemek Toplamı"];
//        }
/////////////mola oranı
//        $row ["breakRate"] = gmdate("H:i:s", ($loginTime * 0.15));
//        if ($this->columnsControl == false) {
//            $columns [] = ["data" => "breakRate", "name" => "breakRate", "title" => "Olması Gereken Mola Süresi"];
//        }

//        $convertCallTimeResult = strtotime($totalCallTimeResult);
//        $convertTotalHoldTimeResult = strtotime($totalHoldTimeResult);
//
//        $lastResult=($loginTime-$totalBreak)/($convertCallTimeResult+$acwSum+$convertTotalHoldTimeResult);
//
//        $row ["productivity"] =($lastResult);
//        if ($this->columnsControl == false) {
//            $columns [] = ["data" => "productivity", "name" => "productivity", "title" => "Verimlilik"];
//        }
////


        $this->columnsControl = true;

        return array($row, $columns);


    }


}