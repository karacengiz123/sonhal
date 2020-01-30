<?php

namespace App\IVR\IgdasBundle\Controller;

use App\Entity\IvrServiceLog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppealStatusController extends AbstractController
{

    /**
     * @Route("/ivr/igdas/pbx/appealStatus/{callId}/{tdcID}")
     * @param Request $request
     * @param $callId
     * @param $tdcID
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function appealStatus(Request $request, $callId, $tdcID)
    {
        $client = new \SoapClient($this->getParameter('igdasApiLink'), array("trace" => 1));
        $em = $this->getDoctrine()->getManager();

        $demands = $client->mtGetTaleplerbyTDC(['pTDCID' => $tdcID]);
        $ivrServiceLog = new IvrServiceLog();
        $ivrServiceLog
            ->setCallId($callId)
            ->setAlias("İgdaş Menü Müracaatın Durumu")
            ->setInput($tdcID)
            ->setRequest($client->__getLastRequest())
            ->setResponse($client->__getLastResponse())
            ->setCreatesAt(new \DateTime());
        $em->persist($ivrServiceLog);
        $em->flush();


//        dump($demands);
//        exit();

        if ($demands->mtGetTaleplerbyTDCResult->_KayitSayisi == 0){

            return $this->json(["tdcID" => $tdcID, "anons" => "", "anons_2" => "", "kayitSayisi" => $demands->mtGetTaleplerbyTDCResult->_KayitSayisi]);

        }else{

            if ($demands->mtGetTaleplerbyTDCResult->_KayitSayisi == 1){

                $anons = "Tesisatınız için oluşturulmuş ".$demands->mtGetTaleplerbyTDCResult->_KayitSayisi." adet müracaat kaydınız bulunmaktadır. Müracaatlarınızın durumu...";

                $anons_2 = "".date('d-m-Y',strtotime($demands->mtGetTaleplerbyTDCResult->_TalepRecords->BBSIVRTalepRecord->_TalepTarihi))." tarihinde. saat. ".$demands->mtGetTaleplerbyTDCResult->_TalepRecords->BBSIVRTalepRecord->_TalepSaat." da. ".$demands->mtGetTaleplerbyTDCResult->_TalepRecords->BBSIVRTalepRecord->_TalepSekli." ile iletmiş olduğunuz ".$demands->mtGetTaleplerbyTDCResult->_TalepRecords->BBSIVRTalepRecord->_TalepKonusu." içerikli müracaatınız. ".$demands->mtGetTaleplerbyTDCResult->_TalepRecords->BBSIVRTalepRecord->_TalepDurumu." . ".date('d-m-Y', strtotime($demands->mtGetTaleplerbyTDCResult->_TalepRecords->BBSIVRTalepRecord->_TalepYasalSure))." tarihinde. saat ".$demands->mtGetTaleplerbyTDCResult->_TalepRecords->BBSIVRTalepRecord->_TalepYasalSaat." a kadar çözümlenerek sonucu tarafınıza ".$demands->mtGetTaleplerbyTDCResult->_TalepRecords->BBSIVRTalepRecord->_TalepBildirimSekli." olarak iletilecektir.";

                return $this->json(["tdcID" => $tdcID, "anons" => $anons, "anons_2" => $anons_2, "kayitSayisi" => $demands->mtGetTaleplerbyTDCResult->_KayitSayisi]);

            }else{

                $anons = "Tesisatınız için oluşturulmuş ".$demands->mtGetTaleplerbyTDCResult->_KayitSayisi." adet müracaat kaydınız bulunmaktadır. Müracaatlarınızın durumu...";
                $record_1 ="";
                foreach ($demands->mtGetTaleplerbyTDCResult->_TalepRecords->BBSIVRTalepRecord as $record){
                    $record_1 .= "".date('d-m-Y', strtotime($record->_TalepTarihi))." tarihinde. saat. ".$record->_TalepSaat." da. ".$record->_TalepSekli." ile iletmiş olduğunuz ".$record->_TalepKonusu." içerikli müracaatınız. ".$record->_TalepDurumu." . ".date('d-m-Y', strtotime($record->_TalepYasalSure))." tarihinde. saat ".$record->_TalepYasalSaat." a kadar. çözümlenerek sonucu tarafınıza ".$record->_TalepBildirimSekli." olarak iletilecektir. ";
                }

                $anons_2 = $record_1;

                return $this->json(["tdcID" => $tdcID, "anons" => $anons, "anons_2" => $anons_2, "kayitSayisi" => $demands->mtGetTaleplerbyTDCResult->_KayitSayisi]);

            }

        }


    }
}