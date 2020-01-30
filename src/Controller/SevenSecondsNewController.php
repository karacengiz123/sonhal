<?php
/**
 * Created by PhpStorm.
 * User: cengi
 * Date: 3.05.2019
 * Time: 11:38
 */

namespace App\Controller;


use App\Asterisk\Entity\Cdr;
use App\Asterisk\Entity\QueueLog;
use App\Asterisk\Entity\IvrLogs;
use App\Entity\BreakType;
use App\Entity\Calls;
use App\Entity\User;
use App\Entity\UserProfile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use spec\LdapTools\Event\LdapObjectSchemaEventSpec;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SevenSecondsNewController extends AbstractController
{

    /**
     * @IsGranted("ROLE_SEVEN_SECOND_NEW")
     * @Route("/sevenSecondNew", name="seven_second_new")
     */
    public function formNew(Request $request)
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
        return $this->render('seven_second/index.html.twig', [
            "form" => $form->createView(),]);
    }

    /**
     * @Route("/sevenSecondsNew")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function sevenNew(Request $request)
    {
        $formValidate = $request->request->all();
        $firstTime = date('Y-m-d H:i:s', strtotime($formValidate["form"]["firstDate"] . "00:00:00"));
        $lastTime = date('Y-m-d H:i:s', strtotime($formValidate["form"]["lastDate"] . "23:59:59"));
        $time = $formValidate["form"]["time"];

//        return $this->json(date('Y-m-d H:i:s',1555020000));
        if ($formValidate["form"]["firstDate"] == "" && $formValidate["form"]["lastDate"] == "") {
            return new Response("Lütfen bir tarih aralığı seçiniz..");
        } else {
            $arr = [];
            if ($formValidate["form"]["firstDate"] == $formValidate["form"]["lastDate"] && $formValidate["form"]["time"] == 86400) {
                $rows = $this->sevenSecondsNew($firstTime, $lastTime);
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
                    $rows = $this->sevenSecondsNew(date('Y-m-d H:i:s', $prevDate),
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
     * @param $lastTime
     * @return array
     */
    public function sevenSecondsNew($firstTime, $lastTime)
    {
        $em = $this->getDoctrine()->getManager();
        $calls = $em->getRepository(Calls::class)
            ->createQueryBuilder('c')
            ->where("c.callStatus=:callStatus")
            ->andWhere("c.callType=:callType")
            ->andWhere("c.durExten <:durExten")
            ->andWhere("c.queue IS NOT NULL")
            ->andWhere("c.user IS NOT NULL")
//            ->andWhere("c.whoCompleted IS NOT NULL")
            ->andwhere('c.dt BETWEEN :startDate AND :endDate')
            ->setParameter("callStatus", "Done")
            ->setParameter("callType", "Inbound")
            ->setParameter("durExten", 8)
            ->setParameter('startDate', $firstTime)
            ->setParameter('endDate', $lastTime)
            ->getQuery()->getResult();
        $row = [];

        foreach ($calls as $call) {
            $arayan = $call->getClid();
            $aranan = $call->getDid();
            $tcNo = $call->getUser()->getUserProfile()->getTckn();
            $name = $call->getUser()->getUserProfile()->__toString();
            $konusmaSuresi = $call->getDurExten();
            if ($call->getwhoCompleted() == "COMPLETEAGENT") {
                $row[] = [
                    "dateRange" => " ",
                    "dateRangeTime" => " ",
                    "arayan" => $arayan,
                    "aranan" => $aranan,
                    "tckn" => $tcNo,
                    "name" => $name,
                    "konusmaSuresi" => $konusmaSuresi,
                    "whoclosed" => "Temsilci"
                ];
            } elseif ($call->getwhoCompleted() == "COMPLETECALLER") {
                $row[] = [
                    "dateRangeTime" => " ",
                    "arayan" => $arayan,
                    "aranan" => $aranan,
                    "tckn" => $tcNo,
                    "name" => $name,
                    "konusmaSuresi" => $konusmaSuresi,
                    "whoclosed" => "Vatandas"
                ];
            }
        }
//        dump($row);
//        exit();

        return $row;
    }
}