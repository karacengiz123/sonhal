<?php
namespace App\Controller;

use App\Asterisk\Entity\Cdr;
use App\Asterisk\Entity\QueueLog;
use App\Asterisk\Entity\Queues;
use App\Asterisk\Entity\Ivr;
use App\Asterisk\Entity\IvrLogs;
use App\Entity\IvrServiceLog;
use Doctrine\ORM\Query;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CardCloseController extends AbstractController
{
    /**
     * @IsGranted("ROLE_CARD_CLOSE")
     * @Route("/card",name="card_close")
     */
    public function card(Request $request)
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

        return $this->render('card_close/index.html.twig', [
            "form" => $form->createView(),
        ]);
    }

    /**
     * @Route("/cardclose")
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    // callid ve response
    public function cardClose(Request $request)
    {
        $formValidate = $request->request->all();
        $firstTime = date('Y-m-d H:i:s', strtotime($formValidate["form"]["firstDate"]."00:00:00"));
        $lastTime = date('Y-m-d H:i:s', strtotime($formValidate["form"]["lastDate"]."23:59:59"));
        $time = $formValidate["form"]["time"];

        if ($formValidate["form"]["firstDate"] == "" && $formValidate["form"]["lastDate"] == ""){
            return new Response("Lütfen bir tarih aralığı seçiniz..");
        }else{
            $arr = [];
            if ($formValidate["form"]["firstDate"] == $formValidate["form"]["lastDate"] && $formValidate["form"]["time"] == 86400){
                $rows = $this->ivrCardClose($firstTime, $lastTime);
                if (count($rows) > 0) {
                    foreach ($rows as $row) {
                        $row["dateRange"] = date('Y-m-d', strtotime($formValidate["form"]["firstDate"] . "00:00:00"));
                        $row["dateRangeTime"] = date('H:i:s', strtotime($formValidate["form"]["firstDate"] . "00:00:00")) . " - " . date('H:i:s', strtotime($formValidate["form"]["lastDate"] . "23:59:59"));

                        $arr[] = $row;
                    }
                }
            }else {
                $lastTime=date("Y-m-d H:i:s",strtotime($lastTime)+86400);

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
                    $rows = $this->ivrCardClose(date('Y-m-d H:i:s', $prevDate), date('Y-m-d H:i:s', $range - 1));
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
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    function ivrCardClose($firstTime,$lastTime){
        $asteriskEm = $this->getDoctrine()->getManager();
        $kartKapama = $asteriskEm->getRepository(IvrServiceLog::class);



        $kartKapama = $kartKapama->createQueryBuilder('ivr')
            ->select('ivr.callId,ivr.response')
            ->Where('ivr.createsAt BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $firstTime)
            ->setParameter('endDate', $lastTime)
            ->andwhere('ivr.alias=:alias')
            ->setParameter('alias', 'İbb Menü Kart Sorgulama')
            ->getQuery()->getArrayResult();

        $row = [];
        foreach ($kartKapama as $ivrLog) {
            $ivrDial=$asteriskEm->getRepository(IvrLogs::class)
                ->createQueryBuilder('dial')
                ->select("dial.choice")
                ->where("dial.callId=:callId")
                ->setParameter("callId",$ivrLog["callId"])
                ->andWhere("dial.ivrId=:ivrId")
                ->setParameter("ivrId",23)
                ->andWhere('dial.dt BETWEEN :startDate AND :endDate')
                ->setParameter('startDate', $firstTime)
                ->setParameter('endDate', $lastTime)
                ->getQuery()->getSingleScalarResult();

            $callRepository=$asteriskEm->getRepository(Cdr::class)
                ->createQueryBuilder('call')
                ->select("call.src")
                ->where("call.callId=:callId")
                ->setParameter("callId",$ivrLog["callId"])
                ->orderBy('call.calldate', 'DESC')
                ->andWhere('call.calldate BETWEEN :startDate AND :endDate')
                ->setParameter('startDate', $firstTime)
                ->setParameter('endDate', $lastTime)
                ->getQuery()->getResult();

            if (count($callRepository) > 0){
                $call = $callRepository[0]["src"];
            }else{
                $call = " ";
            }

            $doc = new \DOMDocument();
            $doc->loadXML($ivrLog["response"]);
            $i = 0;
            foreach ($doc->getElementsByTagNameNS('http://schemas.xmlsoap.org/soap/envelope/', '*') as $elements) {
                $row[] = [
                    "dateRange" => " ",
                    "dateRangeTime" => " ",
                    "dial" => $ivrDial,
                    "callerNumber" =>$call,
                    "cardID" => $elements->getElementsByTagName("MifareId")->item($i)->nodeValue,
                    "cardName" => $elements->getElementsByTagName("ProductName")->item($i)->nodeValue,
                    "cardPressDate" => $elements->getElementsByTagName("CardPressDate")->item($i)->nodeValue,
                ];
                $i++;
            }
        }

        return $row;
    }

}