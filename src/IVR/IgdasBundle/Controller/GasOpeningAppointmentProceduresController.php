<?php

namespace App\IVR\IgdasBundle\Controller;

use App\Entity\IvrServiceLog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GasOpeningAppointmentProceduresController extends AbstractController
{

    /**
     * @Route("/ivr/igdas/pbx/gasOpeningAppointmentProcedures/{callId}/{tdcID}")
     * @param Request $request
     * @param $callId
     * @param $tdcID
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function gasOpeningAppointmentProcedures(Request $request, $callId, $tdcID)
    {
        $client = new \SoapClient($this->getParameter('igdasApiLink'), array("trace" => 1));
        $em = $this->getDoctrine()->getManager();

        $gasOpeningStatus = $client->mtGetGazAcmaDurumByTDC(['pTDCID' => $tdcID]);
        $ivrServiceLog = new IvrServiceLog();
        $ivrServiceLog
            ->setCallId($callId)
            ->setAlias("İgdaş Menü Gaz Açma Randevu İşlemleri")
            ->setInput($tdcID)
            ->setRequest($client->__getLastRequest())
            ->setResponse($client->__getLastResponse())
            ->setCreatesAt(new \DateTime());
        $em->persist($ivrServiceLog);
        $em->flush();

//        dump($gasOpeningStatus);
//        exit();


        if ($gasOpeningStatus->mtGetGazAcmaDurumByTDCResult->_ProjeDurumu == "İlk Gaz Açma"){
            $firstAppointment = $client->mtGetIlkRandevuByTDC(['pTDCID' => $tdcID]);
            $ivrServiceLog = new IvrServiceLog();
            $ivrServiceLog
                ->setCallId($callId)
                ->setAlias("İgdaş Menü Gaz Açma Randevu İşlemleri")
                ->setInput($tdcID)
                ->setRequest($client->__getLastRequest())
                ->setResponse($client->__getLastResponse())
                ->setCreatesAt(new \DateTime());
            $em->persist($ivrServiceLog);
            $em->flush();

            if ($firstAppointment->mtGetIlkRandevuByTDCResult->_RandevuVar == true){
                if ($firstAppointment->mtGetIlkRandevuByTDCResult->_SozlesmeKanali == "SAP"){
                    $anons ="Tesisatınızın gaz açma işlemi. ".$firstAppointment->mtGetIlkRandevuByTDCResult->_RandevuTarih." günü. ".$firstAppointment->mtGetIlkRandevuByTDCResult->_RandevuSaatAraligi." saatleri arasında gerçekleştirilecektir. Randevu saatinde gazı açılacak adreste hazır bulunmanız gerekmektedir. Randevunuzu iptal etmek için tesisatçı firmanızla irtibata geçebilirsiniz. Bir üst menüye dönmek için biri . Müşteri Temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                    return $this->json([
                        "tdcID" => $tdcID,
                        "anons" => $anons,
                        "firstAppointment" => $firstAppointment->mtGetIlkRandevuByTDCResult->_RandevuVar,
                        "payrollAppointment" => "",
                        "dateIsset" => "",
                        "dateTime" => "",
                        "contractChannel" => $firstAppointment->mtGetIlkRandevuByTDCResult->_SozlesmeKanali,
                        "gasOpening" => $gasOpeningStatus->mtGetGazAcmaDurumByTDCResult->_ProjeDurumu
                    ]);
                }else{

                    if ($firstAppointment->mtGetIlkRandevuByTDCResult->_SozlesmeKanali == "WEB"){

                        $anons ="Tesisatınızın gaz açma işlemi ".date("d-m-Y",strtotime($firstAppointment->mtGetIlkRandevuByTDCResult->_RandevuTarih))." günü. ".$firstAppointment->mtGetIlkRandevuByTDCResult->_RandevuSaatAraligi." saatleri arasında gerçekleştirilecektir. Randevu saatinde belgelerinizle birlikte gazı açılacak adreste hazır bulunmanız gerekmektedir. Randevunuzu iptal etmek için tesisatçı firmanızla irtibata geçebilirsiniz. Bir üst menüye dönmek için biri . Müşteri Temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                        return $this->json([
                            "tdcID" => $tdcID,
                            "anons" => $anons,
                            "firstAppointment" => $firstAppointment->mtGetIlkRandevuByTDCResult->_RandevuVar,
                            "payrollAppointment" => "",
                            "dateIsset" => "",
                            "dateTime" => "",
                            "contractChannel" => $firstAppointment->mtGetIlkRandevuByTDCResult->_SozlesmeKanali,
                            "gasOpening" => $gasOpeningStatus->mtGetGazAcmaDurumByTDCResult->_ProjeDurumu
                        ]);
                    }
                }
            }else{

                $anons = "Sayın abonemiz. tesisatınız için henüz gaz açma randevusu alınmamıştır. Lütfen tesisatınızı yaptırdığınız yetkili firma ile irtibata geçerek randevu almasını talep ediniz. Bir üst menüye dönmek için biri . Müşteri Temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                return $this->json([
                    "tdcID" => $tdcID,
                    "anons" => $anons,
                    "firstAppointment" => $firstAppointment->mtGetIlkRandevuByTDCResult->_RandevuVar,
                    "payrollAppointment" => "",
                    "dateIsset" => "",
                    "dateTime" => "",
                    "contractChannel" => "",
                    "gasOpening" => $gasOpeningStatus->mtGetGazAcmaDurumByTDCResult->_ProjeDurumu
                ]);
            }
        }else{

            if ($gasOpeningStatus->mtGetGazAcmaDurumByTDCResult->_ProjeDurumu == "Bordro Gaz Açma"){

                $payrollAppointment = $client->mtGetBordroRandevuByTDC(['pTDCID' => $tdcID]);
                $ivrServiceLog = new IvrServiceLog();
                $ivrServiceLog
                    ->setCallId($callId)
                    ->setAlias("İgdaş Menü Gaz Açma Randevu İşlemleri")
                    ->setInput($tdcID)
                    ->setRequest($client->__getLastRequest())
                    ->setResponse($client->__getLastResponse())
                    ->setCreatesAt(new \DateTime());
                $em->persist($ivrServiceLog);
                $em->flush();

                if ($payrollAppointment->mtGetBordroRandevuByTDCResult->_RandevuVar == true) {

                    if ($payrollAppointment->mtGetBordroRandevuByTDCResult->_RandevuTarih == ""){

                        $anons = "Sayın abonemiz randevu talebiniz mevcuttur. En kısa sürede cep telefonunuza randevu tarihinizi bildiren SMS gönderilecektir. Randevu talebinizi iptal etmek istiyorsanız ikiyi. Bir üst menüye dönmek için biri. Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                        return $this->json([
                            "tdcID" => $tdcID,
                            "anons" => $anons,
                            "firstAppointment" => "",
                            "payrollAppointment" => $payrollAppointment->mtGetBordroRandevuByTDCResult->_RandevuVar,
                            "dateIsset" => "",
                            "dateTime" => "",
                            "contractChannel" => "",
                            "gasOpening" => $gasOpeningStatus->mtGetGazAcmaDurumByTDCResult->_ProjeDurumu
                        ]);

                    }else{

                        if (strtotime(date("d-m-Y",strtotime($payrollAppointment->mtGetBordroRandevuByTDCResult->_RandevuTarih))) < strtotime(date("d-m-Y"))) {

                            $pastAppointment = $client->mtRandevuGecmisByTDC(['pTDCID' => $tdcID]);
                            $ivrServiceLog = new IvrServiceLog();
                            $ivrServiceLog
                                ->setCallId($callId)
                                ->setAlias("İgdaş Menü Gaz Açma Randevu İşlemleri")
                                ->setInput($tdcID)
                                ->setRequest($client->__getLastRequest())
                                ->setResponse($client->__getLastResponse())
                                ->setCreatesAt(new \DateTime());
                            $em->persist($ivrServiceLog);
                            $em->flush();

                            $anons = "Sayın abonemiz.  " . date("d-m-Y",strtotime($pastAppointment->mtRandevuGecmisByTDCResult->_RandevuTarih)) . " günü. saat: " . $pastAppointment->mtRandevuGecmisByTDCResult->_RandevuTarih . " te adresinize gelinmiş. fakat " . $pastAppointment->mtRandevuGecmisByTDCResult->_Neden . " nedeni ile gazınız açılmamıştır. Gerekli düzenleme yapıldıktan sonra tekrar randevu alabilirsiniz. Gerekli düzenlemeleri yaptıysanız randevu almak için biri . Bir üst menüye dönmek için ikiyi . Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                            return $this->json([
                                "tdcID" => $tdcID,
                                "anons" => $anons,
                                "firstAppointment" => "",
                                "payrollAppointment" => $payrollAppointment->mtGetBordroRandevuByTDCResult->_RandevuVar,
                                "dateIsset" => "1",
                                "dateTime" => "1",
                                "contractChannel" => "",
                                "gasOpening" => $gasOpeningStatus->mtGetGazAcmaDurumByTDCResult->_ProjeDurumu
                            ]);

                        } else {

                            if ($payrollAppointment->mtGetBordroRandevuByTDCResult->_SozlesmeKanali == "SAP") {

                                $anons = "Tesisatınızın gaz açma işlemi. " . date("d-m-Y",strtotime($payrollAppointment->mtGetBordroRandevuByTDCResult->_RandevuTarih)) . " günü. " . $payrollAppointment->mtGetBordroRandevuByTDCResult->_RandevuSaatAraligi . " saatleri arasında gerçekleştirilecektir. Randevu saatinde gazı açılacak adreste hazır bulunmanız gerekmektedir. Randevu talebinizi iptal etmek istiyorsanız ikiyi . Bir üst menüye dönmek için  biri . Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                                return $this->json([
                                    "tdcID" => $tdcID,
                                    "anons" => $anons,
                                    "firstAppointment" => "",
                                    "payrollAppointment" => $payrollAppointment->mtGetBordroRandevuByTDCResult->_RandevuVar,
                                    "dateIsset" => "1",
                                    "dateTime" => "",
                                    "contractChannel" => $payrollAppointment->mtGetBordroRandevuByTDCResult->_SozlesmeKanali,
                                    "gasOpening" => $gasOpeningStatus->mtGetGazAcmaDurumByTDCResult->_ProjeDurumu
                                ]);

                            } else {

                                if ($payrollAppointment->mtGetBordroRandevuByTDCResult->_SozlesmeKanali == "WEB") {

                                    $anons = "Tesisatınızın gaz açma işlemi " . $payrollAppointment->mtGetBordroRandevuByTDCResult->_RandevuTarih . " günü. " . $payrollAppointment->mtGetBordroRandevuByTDCResult->_RandevuSaatAraligi . " saatleri arasında gerçekleştirilecektir. Randevu saatinde belgelerinizle birlikte gazı açılacak adreste hazır bulunmanız gerekmektedir. Randevu talebinizi iptal etmek istiyorsanız ikiyi . Bir üst menüye dönmek için biri . Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                                    return $this->json([
                                        "tdcID" => $tdcID,
                                        "anons" => $anons,
                                        "firstAppointment" => "",
                                        "payrollAppointment" => $payrollAppointment->mtGetBordroRandevuByTDCResult->_RandevuVar,
                                        "dateIsset" => "1",
                                        "dateTime" => "",
                                        "contractChannel" => $payrollAppointment->mtGetBordroRandevuByTDCResult->_SozlesmeKanali,
                                        "gasOpening" => $gasOpeningStatus->mtGetGazAcmaDurumByTDCResult->_ProjeDurumu
                                    ]);
                                }
                            }
                        }
                    }

                }else{

                    $anons = "Sayın abonemiz. tesisatınız için gaz açma randevu kaydı bulunmamaktadır. Randevu almak için biri. Bir üst menüye dönmek için ikiyi. Müşteri Temsilcisine bağlanmak için lütfen sıfırı tuşlayınız";

                    return $this->json([
                        "tdcID" => $tdcID,
                        "anons" => $anons,
                        "firstAppointment" => "",
                        "payrollAppointment" => $payrollAppointment->mtGetBordroRandevuByTDCResult->_RandevuVar,
                        "dateIsset" => "",
                        "dateTime" => "",
                        "contractChannel" => "",
                        "gasOpening" => $gasOpeningStatus->mtGetGazAcmaDurumByTDCResult->_ProjeDurumu
                    ]);

                }

            }else{

                if ($gasOpeningStatus->mtGetGazAcmaDurumByTDCResult->_ProjeDurumu == "Teknik Gaz Açma"){

                    $anons = "Sayın abonemiz. tesisatınız için gaz açma randevu kaydı bulunmamaktadır. Randevu almak için biri. Bir üst menüye dönmek için ikiyi. Müşteri Temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                    return $this->json([
                        "tdcID" => $tdcID,
                        "anons" => $anons,
                        "firstAppointment" => "",
                        "payrollAppointment" => "",
                        "dateIsset" => "",
                        "dateTime" => "",
                        "contractChannel" => "",
                        "gasOpening" => $gasOpeningStatus->mtGetGazAcmaDurumByTDCResult->_ProjeDurumu
                    ]);

                }else{

                    return $this->json([
                        "tdcID" => $tdcID,
                        "anons" => "",
                        "firstAppointment" => "",
                        "payrollAppointment" => "",
                        "dateIsset" => "",
                        "dateTime" => "",
                        "contractChannel" => "",
                        "gasOpening" => ""
                    ]);

                }

            }

        }


    }

}