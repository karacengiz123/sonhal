<?php

namespace App\IVR\IgdasBundle\Controller;

use App\Entity\IvrServiceLog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WelcomeMenuSController extends AbstractController
{

    /**
     * @Route("/ivr/igdas/pbx/welcomeMenuS/{callId}/{tdcID}")
     * @param Request $request
     * @param $callId
     * @param $tdcID
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function welcomeMenuSappointment(Request $request, $callId, $tdcID)
    {
        $client = new \SoapClient($this->getParameter('igdasApiLink'), array("trace" => 1));
        $em = $this->getDoctrine()->getManager();

        $gasOpening = $client->mtGetGazAcmaDurumByTDC(['pTDCID' => $tdcID]);
        $ivrServiceLog = new IvrServiceLog();
        $ivrServiceLog
            ->setCallId($callId)
            ->setAlias("İgdaş Menü Karşılama S")
            ->setInput($tdcID)
            ->setRequest($client->__getLastRequest())
            ->setResponse($client->__getLastResponse())
            ->setCreatesAt(new \DateTime());
        $em->persist($ivrServiceLog);
        $em->flush();

//        dump($gasOpening);
//        exit();

        if ($gasOpening->mtGetGazAcmaDurumByTDCResult->_ProjeDurumu == "İlk Gaz Açma"){

            $firstAppointment = $client->mtGetIlkRandevuByTDC(['pTDCID' => $tdcID]);
            $ivrServiceLog = new IvrServiceLog();
            $ivrServiceLog
                ->setCallId($callId)
                ->setAlias("İgdaş Menü Karşılama S")
                ->setInput($tdcID)
                ->setRequest($client->__getLastRequest())
                ->setResponse($client->__getLastResponse())
                ->setCreatesAt(new \DateTime());
            $em->persist($ivrServiceLog);
            $em->flush();

//            dump($firstAppointment);
//            exit();

            if ($firstAppointment->mtGetIlkRandevuByTDCResult->_RandevuVar == true){

                if (strtotime(date("d-m-Y",strtotime($firstAppointment->mtGetIlkRandevuByTDCResult->_RandevuTarih))) < strtotime(date("d-m-Y"))){

                    $pastAppointment = $client->mtRandevuGecmisByTDC(['pTDCID' => $tdcID]);
                    $ivrServiceLog = new IvrServiceLog();
                    $ivrServiceLog
                        ->setCallId($callId)
                        ->setAlias("İgdaş Menü Karşılama S")
                        ->setInput($tdcID)
                        ->setRequest($client->__getLastRequest())
                        ->setResponse($client->__getLastResponse())
                        ->setCreatesAt(new \DateTime());
                    $em->persist($ivrServiceLog);
                    $em->flush();

                    $anons = "Sayın abonemiz.  " . date("d-m-Y",strtotime($pastAppointment->mtRandevuGecmisByTDCResult->_RandevuTarih)) . " günü. saat: " . $pastAppointment->mtRandevuGecmisByTDCResult->_RandevuSaat . " te adresinize gelinmiş. Gazınız açılmamıştır. Firmanız ile irtibata geçmeniz. Gerekli düzenleme yapıldıktan sonra tekrar randevu alınması gerekmektedir. Müşteri temsilcisi ile görüşmek için sıfırı tuşlayınız.";

                    return $this->json([
                        "tdcID" => $tdcID,
                        "anons" => $anons,
                        "anons_2" => "",
                        "anons_3" => "",
                        "gasOpening" => $gasOpening->mtGetGazAcmaDurumByTDCResult->_ProjeDurumu,
                        "firstAppointment" => 1,
                        "payrollAppointment" => 0,
                        "dateIsset" => "1",
                        "dateTime" => "1",
                        "contractChannel" => ""
                    ]);

                } else {

                    if ($firstAppointment->mtGetIlkRandevuByTDCResult->_SozlesmeKanali == "SAP") {

                        $anons = "Tesisatınızın gaz açma işlemi. " . date("d-m-Y",strtotime($firstAppointment->mtGetIlkRandevuByTDCResult->_RandevuTarih)) . " günü. " . $firstAppointment->mtGetIlkRandevuByTDCResult->_RandevuSaatAraligi . " saatleri arasında gerçekleştirilecektir. Randevu saatinde gazı açılacak adreste hazır bulunmanız gerekmektedir. Randevunuzu iptal etmek için tesisatçı firmanızla irtibata geçebilirsiniz.";

                        $anons_3 = " Proje Durumu Hakkında Detaylı Bilgi Almak için dördü. Sözleşme Fesih İşlemleri hakkında bilgi almak için beşi. Abonelik ve Sözleşme Şartları. Doğalgaz Birim Fiyatları. Fatura Ödeme Noktaları. İGDAŞ Hizmet Binaları. Tesisatçı Firmalar ve Kampanyalar Hakkında Bilgi Almak için altıyı. Müracaatınızın durumu hakkında bilgi almak için yediyi. Daha Ekonomik ve Verimli Doğalgaz Kullanımı hakkında bilgi almak için sekizi. Öneri ve Şikayetlerinizi Sesli Mesaj yoluyla bizlere iletmek için dokuzu. Müşteri Temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                        return $this->json([
                            "tdcID" => $tdcID,
                            "anons" => $anons,
                            "anons_2" => "",
                            "anons_3" => $anons_3,
                            "gasOpening" => $gasOpening->mtGetGazAcmaDurumByTDCResult->_ProjeDurumu,
                            "firstAppointment" => 1,
                            "payrollAppointment" => 0,
                            "dateIsset" => "",
                            "dateTime" => "",
                            "contractChannel" => $firstAppointment->mtGetIlkRandevuByTDCResult->_SozlesmeKanali
                        ]);

                    } else {

                        if ($firstAppointment->mtGetIlkRandevuByTDCResult->_SozlesmeKanali == "WEB") {

                            $anons = "Tesisatınızın gaz açma işlemi " . date("d-m-Y",strtotime($firstAppointment->mtGetIlkRandevuByTDCResult->_RandevuTarih)) . " günü. " . $firstAppointment->mtGetIlkRandevuByTDCResult->_RandevuSaatAraligi . " saatleri arasında gerçekleştirilecektir. Randevu saatinde belgelerinizle birlikte gazı açılacak adreste hazır bulunmanız gerekmektedir. Randevunuzu iptal etmek için tesisatçı firmanızla irtibata geçebilirsiniz.";

                            $anons_3 = " Proje Durumu Hakkında Detaylı Bilgi Almak için dördü. Sözleşme Fesih İşlemleri hakkında bilgi almak için beşi. Abonelik ve Sözleşme Şartları. Doğalgaz Birim Fiyatları. Fatura Ödeme Noktaları. İGDAŞ Hizmet Binaları. Tesisatçı Firmalar ve Kampanyalar Hakkında Bilgi Almak için altıyı. Müracaatınızın durumu hakkında bilgi almak için yediyi. Daha Ekonomik ve Verimli Doğalgaz Kullanımı hakkında bilgi almak için sekizi. Öneri ve Şikayetlerinizi Sesli Mesaj yoluyla bizlere iletmek için dokuzu. Müşteri Temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                            return $this->json([
                                "tdcID" => $tdcID,
                                "anons" => $anons,
                                "anons_2" => "",
                                "anons_3" => $anons_3,
                                "gasOpening" => $gasOpening->mtGetGazAcmaDurumByTDCResult->_ProjeDurumu,
                                "firstAppointment" => 1,
                                "payrollAppointment" => 0,
                                "dateIsset" => "",
                                "dateTime" => "",
                                "contractChannel" => $firstAppointment->mtGetIlkRandevuByTDCResult->_SozlesmeKanali
                            ]);

                        }

                    }

                }

            }else{

                $anons = "Sayın abonemiz. tesisatınız için henüz gaz açma randevusu alınmamıştır. Lütfen tesisatınızı yaptırdığınız yetkili firma ile irtibata geçerek randevu almasını talep ediniz.";

                $anons_3 = " Proje Durumu Hakkında Detaylı Bilgi Almak için dördü. Sözleşme Fesih İşlemleri hakkında bilgi almak için beşi. Abonelik ve Sözleşme Şartları. Doğalgaz Birim Fiyatları. Fatura Ödeme Noktaları. İGDAŞ Hizmet Binaları. Tesisatçı Firmalar ve Kampanyalar Hakkında Bilgi Almak için altıyı. Müracaatınızın durumu hakkında bilgi almak için yediyi. Daha Ekonomik ve Verimli Doğalgaz Kullanımı hakkında bilgi almak için sekizi. Öneri ve Şikayetlerinizi Sesli Mesaj yoluyla bizlere iletmek için dokuzu. Müşteri Temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                return $this->json([
                    "tdcID" => $tdcID,
                    "anons" => $anons,
                    "anons_2" => "",
                    "anons_3" => $anons_3,
                    "gasOpening" => $gasOpening->mtGetGazAcmaDurumByTDCResult->_ProjeDurumu,
                    "firstAppointment" => 0,
                    "payrollAppointment" => 0,
                    "dateIsset" => "",
                    "dateTime" => "",
                    "contractChannel" => $firstAppointment->mtGetIlkRandevuByTDCResult->_SozlesmeKanali
                ]);

            }

        }else{

            if ($gasOpening->mtGetGazAcmaDurumByTDCResult->_ProjeDurumu == "Bordro Gaz Açma"){

                $payrollAppointment = $client->mtGetBordroRandevuByTDC(['pTDCID' => $tdcID]);
                $ivrServiceLog = new IvrServiceLog();
                $ivrServiceLog
                    ->setCallId($callId)
                    ->setAlias("İgdaş Menü Karşılama S")
                    ->setInput($tdcID)
                    ->setRequest($client->__getLastRequest())
                    ->setResponse($client->__getLastResponse())
                    ->setCreatesAt(new \DateTime());
                $em->persist($ivrServiceLog);
                $em->flush();

//        Sözleşme Kanalı "SAP" olarak dönüyor ona bakılacak

//        dump($payrollAppointment);
//        exit();

                if ($payrollAppointment->mtGetBordroRandevuByTDCResult->_RandevuVar == true) {

                    if (strtotime(date("d-m-Y",strtotime($payrollAppointment->mtGetBordroRandevuByTDCResult->_RandevuTarih))) < strtotime(date("d-m-Y"))) {

                        $pastAppointment = $client->mtRandevuGecmisByTDC(['pTDCID' => $tdcID]);
                        $ivrServiceLog = new IvrServiceLog();
                        $ivrServiceLog
                            ->setCallId($callId)
                            ->setAlias("İgdaş Menü Karşılama S")
                            ->setInput($tdcID)
                            ->setRequest($client->__getLastRequest())
                            ->setResponse($client->__getLastResponse())
                            ->setCreatesAt(new \DateTime());
                        $em->persist($ivrServiceLog);
                        $em->flush();

                        $anons = "Sayın abonemiz.  " . date("d-m-Y",strtotime($pastAppointment->mtRandevuGecmisByTDCResult->_RandevuTarih)) . " günü. saat: " . $pastAppointment->mtRandevuGecmisByTDCResult->_RandevuTarih . " te adresinize gelinmiş. fakat " . $pastAppointment->mtRandevuGecmisByTDCResult->_Neden . " nedeni ile gazınız açılmamıştır. Gerekli düzenleme yapıldıktan sonra tekrar randevu alabilirsiniz. Gerekli düzenlemeleri yaptıysanız randevu almak için biri tuşlayınız.";

                        $anons_2 = "Sayın abonemiz randevu talebiniz alınmıştır. En kısa sürede cep telefonunuza randevu tarihinizi bildiren SMS gönderilecektir.";

                        $anons_3 = "Proje Durumu Hakkında Detaylı Bilgi Almak için dördü. Sözleşme Fesih İşlemleri hakkında bilgi almak için beşi. Abonelik ve Sözleşme Şartları. Doğalgaz Birim Fiyatları. Fatura Ödeme Noktaları. İGDAŞ Hizmet Binaları. Tesisatçı Firmalar ve Kampanyalar Hakkında Bilgi Almak için altıyı. Müracaatınızın durumu hakkında bilgi almak için yediyi. Daha Ekonomik ve Verimli Doğalgaz Kullanımı hakkında bilgi almak için sekizi. Öneri ve Şikayetlerinizi Sesli Mesaj yoluyla bizlere iletmek için dokuzu. Müşteri Temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                        return $this->json([
                            "tdcID" => $tdcID,
                            "anons" => $anons,
                            "anons_2" => $anons_2,
                            "anons_3" => $anons_3,
                            "gasOpening" => $gasOpening->mtGetGazAcmaDurumByTDCResult->_ProjeDurumu,
                            "firstAppointment" => 0,
                            "payrollAppointment" => 1,
                            "dateIsset" => "1",
                            "dateTime" => "1",
                            "contractChannel" => ""
                        ]);

                    } else {

                        if ($payrollAppointment->mtGetBordroRandevuByTDCResult->_SozlesmeKanali == "SAP") {

                            $anons = "Tesisatınızın  " . date("d-m-Y",strtotime($payrollAppointment->mtGetBordroRandevuByTDCResult->_RandevuTarih)) . " günü. " . $payrollAppointment->mtGetBordroRandevuByTDCResult->_RandevuSaatAraligi . " saatleri arasında gaz açma randevusu bulunmaktadır. Randevu saatinde gazı açılacak adreste hazır bulunmanız gerekmektedir. Bilgileri tekrar dinlemek için biri. Randevu talebinizi iptal etmek istiyorsanız ikiyi tuşlayınız.";

                            $anons_2 = "Sayın abonemiz randevu talebiniz iptal edilmiştir. Tekrar randevu almak için bizi arayınız.";

                            $anons_3 = " Proje Durumu Hakkında Detaylı Bilgi Almak için dördü. Sözleşme Fesih İşlemleri hakkında bilgi almak için beşi. Abonelik ve Sözleşme Şartları. Doğalgaz Birim Fiyatları. Fatura Ödeme Noktaları. İGDAŞ Hizmet Binaları. Tesisatçı Firmalar ve Kampanyalar Hakkında Bilgi Almak için altıyı. Müracaatınızın durumu hakkında bilgi almak için yediyi. Daha Ekonomik ve Verimli Doğalgaz Kullanımı hakkında bilgi almak için sekizi. Öneri ve Şikayetlerinizi Sesli Mesaj yoluyla bizlere iletmek için dokuzu. Müşteri Temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                            return $this->json([
                                "tdcID" => $tdcID,
                                "anons" => $anons,
                                "anons_2" => $anons_2,
                                "anons_3" => $anons_3,
                                "gasOpening" => $gasOpening->mtGetGazAcmaDurumByTDCResult->_ProjeDurumu,
                                "firstAppointment" => 0,
                                "payrollAppointment" => 1,
                                "dateIsset" => "1",
                                "dateTime" => "",
                                "contractChannel" => $payrollAppointment->mtGetBordroRandevuByTDCResult->_SozlesmeKanali
                            ]);

                        } else {

                            if ($payrollAppointment->mtGetBordroRandevuByTDCResult->_SozlesmeKanali == "WEB") {

                                $anons = "Tesisatınızın gaz açma işlemi " . date("d-m-Y",strtotime($payrollAppointment->mtGetBordroRandevuByTDCResult->_RandevuTarih)) . " günü. " . $payrollAppointment->mtGetBordroRandevuByTDCResult->_RandevuSaatAraligi . " saatleri arasında gerçekleştirilecektir. Randevu saatinde belgelerinizle birlikte gazı açılacak adreste hazır bulunmanız gerekmektedir. Bilgileri tekrar dinlemek için biri. Randevu talebinizi iptal etmek istiyorsanız ikiyi tuşlayınız.";

                                $anons_2 = "Sayın abonemiz randevu talebiniz iptal edilmiştir. Tekrar randevu almak için bizi arayınız.";

                                $anons_3 = " Proje Durumu Hakkında Detaylı Bilgi Almak için dördü. Sözleşme Fesih İşlemleri hakkında bilgi almak için beşi. Abonelik ve Sözleşme Şartları. Doğalgaz Birim Fiyatları. Fatura Ödeme Noktaları. İGDAŞ Hizmet Binaları. Tesisatçı Firmalar ve Kampanyalar Hakkında Bilgi Almak için altıyı. Müracaatınızın durumu hakkında bilgi almak için yediyi. Daha Ekonomik ve Verimli Doğalgaz Kullanımı hakkında bilgi almak için sekizi. Öneri ve Şikayetlerinizi Sesli Mesaj yoluyla bizlere iletmek için dokuzu. Müşteri Temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                                return $this->json([
                                    "tdcID" => $tdcID,
                                    "anons" => $anons,
                                    "anons_2" => $anons_2,
                                    "anons_3" => $anons_3,
                                    "gasOpening" => $gasOpening->mtGetGazAcmaDurumByTDCResult->_ProjeDurumu,
                                    "firstAppointment" => 0,
                                    "payrollAppointment" => 1,
                                    "dateIsset" => "1",
                                    "dateTime" => "",
                                    "contractChannel" => $payrollAppointment->mtGetBordroRandevuByTDCResult->_SozlesmeKanali
                                ]);

                            }

                        }

                    }

                }elseif ($payrollAppointment->mtGetBordroRandevuByTDCResult->_RandevuVar == false){

                    if ($payrollAppointment->mtGetBordroRandevuByTDCResult->_RandevuDurumu == 2) {

                        $anons = "Sayın abonemiz randevu talebiniz mevcuttur. En kısa sürede cep telefonunuza randevu tarihinizi bildiren SMS gönderilecektir. Randevu talebinizi iptal etmek istiyorsanız ikiyi tuşlayınız.";

                        $anons_2 = "Sayın abonemiz randevu talebiniz iptal edilmiştir. Tekrar randevu almak için bizi arayınız.";

                        $anons_3 = " Proje Durumu Hakkında Detaylı Bilgi Almak için dördü. Sözleşme Fesih İşlemleri hakkında bilgi almak için beşi. Abonelik ve Sözleşme Şartları. Doğalgaz Birim Fiyatları. Fatura Ödeme Noktaları. İGDAŞ Hizmet Binaları. Tesisatçı Firmalar ve Kampanyalar Hakkında Bilgi Almak için altıyı. Müracaatınızın durumu hakkında bilgi almak için yediyi. Daha Ekonomik ve Verimli Doğalgaz Kullanımı hakkında bilgi almak için sekizi. Öneri ve Şikayetlerinizi Sesli Mesaj yoluyla bizlere iletmek için dokuzu. Müşteri Temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                        return $this->json([
                            "tdcID" => $tdcID,
                            "anons" => $anons,
                            "anons_2" => $anons_2,
                            "anons_3" => $anons_3,
                            "gasOpening" => $gasOpening->mtGetGazAcmaDurumByTDCResult->_ProjeDurumu,
                            "firstAppointment" => 0,
                            "payrollAppointment" => 1,
                            "dateIsset" => "",
                            "dateTime" => "",
                            "contractChannel" => ""
                        ]);

                    }elseif ($payrollAppointment->mtGetBordroRandevuByTDCResult->_RandevuDurumu == 1){

                        $anons_3 = " Proje Durumu Hakkında Detaylı Bilgi Almak için dördü. Sözleşme Fesih İşlemleri hakkında bilgi almak için beşi. Abonelik ve Sözleşme Şartları. Doğalgaz Birim Fiyatları. Fatura Ödeme Noktaları. İGDAŞ Hizmet Binaları. Tesisatçı Firmalar ve Kampanyalar Hakkında Bilgi Almak için altıyı. Müracaatınızın durumu hakkında bilgi almak için yediyi. Daha Ekonomik ve Verimli Doğalgaz Kullanımı hakkında bilgi almak için sekizi. Öneri ve Şikayetlerinizi Sesli Mesaj yoluyla bizlere iletmek için dokuzu. Müşteri Temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                        return $this->json([
                            "tdcID" => $tdcID,
                            "anons" => "",
                            "anons_2" => "",
                            "anons_3" => $anons_3,
                            "gasOpening" => $gasOpening->mtGetGazAcmaDurumByTDCResult->_ProjeDurumu,
                            "firstAppointment" => 0,
                            "payrollAppointment" => 0,
                            "dateIsset" => "",
                            "dateTime" => "",
                            "contractChannel" => ""
                        ]);

                    }

                }

            }else{

                if ($gasOpening->mtGetGazAcmaDurumByTDCResult->_ProjeDurumu == "Teknik Gaz Açma"){

                    $anons = "Sayın abonemiz. tesisatınızda teknik eksiklikten dolayı gaz arzınız sağlanamamıştır. Lütfen Sertifikalı Tesisatçı Firma ile irtibata geçiniz. Eksiklikler giderildikten sonra. randevuyu. Sertifikalı Firma alacaktır. Bir üst menüye dönmek için biri. Müşteri Temsilcisine bağlanmak için lütfen sıfırı tuşlayınız";

                    return $this->json([
                        "tdcID" => $tdcID,
                        "anons" => $anons,
                        "anons_2" => "",
                        "anons_3" => "",
                        "gasOpening" => $gasOpening->mtGetGazAcmaDurumByTDCResult->_ProjeDurumu,
                        "firstAppointment" => 0,
                        "payrollAppointment" => 0,
                        "dateIsset" => "",
                        "dateTime" => "",
                        "contractChannel" => ""
                    ]);

                }else{
                    $anons = "Sayın abonemiz. tesisatınız için gaz açma randevu kaydı bulunmamaktadır. Randevu almak için biri tuşuna basınız.";
                    //$anons = "Sayın abonemiz. tesisatınız için gaz açma randevu kaydı bulunmamaktadır. Randevu almak için biri.  Bir üst menüye dönmek için ikiyi. Müşteri Temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                    $anons_2 = "Sayın abonemiz randevu talebiniz alınmıştır. En kısa sürede cep telefonunuza randevu tarihinizi bildiren SMS gönderilecektir.";

                    $anons_3 = " Proje Durumu Hakkında Detaylı Bilgi Almak için dördü. Sözleşme Fesih İşlemleri hakkında bilgi almak için beşi. Abonelik ve Sözleşme Şartları. Doğalgaz Birim Fiyatları. Fatura Ödeme Noktaları. İGDAŞ Hizmet Binaları. Tesisatçı Firmalar ve Kampanyalar Hakkında Bilgi Almak için altıyı. Müracaatınızın durumu hakkında bilgi almak için yediyi. Daha Ekonomik ve Verimli Doğalgaz Kullanımı hakkında bilgi almak için sekizi. Öneri ve Şikayetlerinizi Sesli Mesaj yoluyla bizlere iletmek için dokuzu. Müşteri Temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                    return $this->json([
                        "tdcID" => $tdcID,
                        "anons" => $anons,
                        "anons_2" => $anons_2,
                        "anons_3" => $anons_3,
                        "gasOpening" => "",
                        "firstAppointment" => 0,
                        "payrollAppointment" => 0,
                        "dateIsset" => "",
                        "dateTime" => "",
                        "contractChannel" => ""
                    ]);

                }
            }

        }

    }

}