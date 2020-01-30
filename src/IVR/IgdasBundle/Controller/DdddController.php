<?php

namespace App\IVR\IgdasBundle\Controller;

use App\Entity\IvrServiceLog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DdddController extends AbstractController
{

    /**
     * @Route("/ivr/igdas/pbx/ddd/{tdcNo}")
     * @param Request $request
     * @param $tdcNo
     * @return Response
     */
    public function ddd(Request $request, $tdcNo)
    {
        $client = new \SoapClient($this->getParameter('igdasApiLink'), array("trace" => 1));

//        $em = $this->getDoctrine()->getManager();
//        $mtCreateCaseForDesktop = $client->mtCreateCaseForDesktop([
//            "pTDCID" => "",
//            "pAccountID" => "8736BD09-D5C7-E811-801C-0050568F49E3",
//            "pTelefonNo" => "05072303833",
//            "pBaslik" => "Yeni Talep",
//            "pTalepAltKonu" => "?",
//            "pIVRDurumKodu" => 0,
//            "pSon3Menu" => "?",
//            "pTalepTipi" => "Bilgi",
//            "pState" => "Resolved",
//            "pAgent" => "salih.inci",
//            "pCallID" => "A2BB501F-464A11E9-BFF99A66-73656183@10.5.95.149",
//            "pAciklama" => ""
//        ]);
//
//
//        dump($mtCreateCaseForDesktop);
//        exit();
//
//        $url = "http://igcrmiis.igdas.com.tr/IgdasCRM/main.aspx?etc=112&extraqs=%3f_gridType%3d112%26etc%3d112%26id%3d%257b"+entityId+"%257d%26pagemode%3diframe%26preloadcache%3d1552905984187%26rskey%3d16452833&pagetype=entityrecord";



////        return new JsonResponse($createCase->mtCreateCaseResult);
//        $ivrServiceLog = new IvrServiceLog();
//        $ivrServiceLog
//            ->setCallId("1")
//            ->setAlias("İgdaş Menü Ivr Aktivite Oluştur Test")
//            ->setInput("1")
//            ->setRequest($client->__getLastRequest())
//            ->setResponse($client->__getLastResponse())
//            ->setCreatesAt(new \DateTime());
//        $em->persist($ivrServiceLog);
//        $em->flush();
//        dump($createCase);
//        exit();


        $tdcID = $client->mtGetTesisatByTDC([
            'pTDCno' => $tdcNo
        ]);

//        $tdcID = $tdcID->mtGetTesisatByTDCResult->_TDCID;

//        $demands = $client->mtgetFesihDurumu(['pSozlesmeHesapNo' => $tdcNo]);


        dump($tdcID);

        $gasOpening = $client->mtGetGazAcmaDurumByTDC(['pTDCID' => $tdcID->mtGetTesisatByTDCResult->_TDCID]);
        $firstAppointment = $client->mtGetIlkRandevuByTDC(['pTDCID' => $tdcID->mtGetTesisatByTDCResult->_TDCID]);
//        $pastAppointment = $client->mtRandevuGecmisByTDC(['pTDCID' => $tdcID->mtGetTesisatByTDCResult->_TDCID]);

        $payrollAppointment = $client->mtGetBordroRandevuByTDC(['pTDCID' => $tdcID->mtGetTesisatByTDCResult->_TDCID]);
        $pastAppointment = $client->mtRandevuGecmisByTDC(['pTDCID' => $tdcID->mtGetTesisatByTDCResult->_TDCID]);


        dump($gasOpening);
        dump($firstAppointment);
        dump($payrollAppointment);
        dump($pastAppointment);


        exit();




        return $this->json([
            "tdcID" => $tdcID,
            "anons" => $anons,
            "firstAppointment" => $firstAppointment->mtGetIlkRandevuByTDCResult->_RandevuVar,
            "payrollAppointment" => "",
            "dateIsset" => "",
            "dateTime" => "",
            "contractChannel" => $firstAppointment->mtGetIlkRandevuByTDCResult->_SozlesmeKanali,
            "gasOpeningStatus" => $gasOpeningStatus->mtGetGazAcmaDurumByTDCResult->_ProjeDurumu
        ]);



        return $this->json([
            "tdcID" => $tdcID,
            "anons" => $anons,
            "payrollAppointment" => $payrollAppointment->mtGetBordroRandevuByTDCResult->_RandevuVar,
            "dateIsset" => "1",
            "dateTime" => "0",
            "contractChannel" => $payrollAppointment->mtGetBordroRandevuByTDCResult->_SozlesmeKanali,
            "gasOpeningStatus" => $gasOpeningStatus->mtGetGazAcmaDurumByTDCResult->_ProjeDurumu
        ]);







//        Kullanıcı Takımları Oluştururken Bir Tane Takımın Yöneticileri Olacak.


    }
}