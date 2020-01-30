<?php

namespace App\IVR\IgdasBundle\Controller;

use App\Entity\IvrServiceLog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WelcomeMenuFController extends AbstractController
{

    /**
     * @Route("/ivr/igdas/pbx/welcomeMenuF/{callId}/{tdcNo}")
     * @param Request $request
     * @param $callId
     * @param $tdcNo
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function welcomeMenuF(Request $request, $callId, $tdcNo)
    {
        $client = new \SoapClient($this->getParameter('igdasApiLink'), array("trace" => 1));
        $em = $this->getDoctrine()->getManager();

        $tdcID = $client->mtGetTesisatByTDC(['pTDCno' => $tdcNo]);
        $ivrServiceLog = new IvrServiceLog();
        $ivrServiceLog
            ->setCallId($callId)
            ->setAlias("İgdaş Menü Karşılama F")
            ->setInput($tdcNo)
            ->setRequest($client->__getLastRequest())
            ->setResponse($client->__getLastResponse())
            ->setCreatesAt(new \DateTime());
        $em->persist($ivrServiceLog);
        $em->flush();

        $lawRegistration = $client->mtHukukKaydiVarmi(['pSozlesmeHesapNo' => $tdcNo]);
        $ivrServiceLog = new IvrServiceLog();
        $ivrServiceLog
            ->setCallId($callId)
            ->setAlias("İgdaş Menü Karşılama F")
            ->setInput($tdcNo)
            ->setRequest($client->__getLastRequest())
            ->setResponse($client->__getLastResponse())
            ->setCreatesAt(new \DateTime());
        $em->persist($ivrServiceLog);
        $em->flush();
//        $lawRegistration = $lawRegistration->mtHukukKaydiVarmiResult->_cevap;

//        dump($lawRegistration);
//        exit();

        // TDCID Değişim Yeri
        $tdcID = $tdcID->mtGetTesisatByTDCResult->_TDCID;

        if ($lawRegistration->mtHukukKaydiVarmiResult->_cevap == true){

//            dump($tdcID);

            $invoiceDebtF = $client->mtGetFaturaBorcFByTDC(['pTDCID' => $tdcID]);
            $ivrServiceLog = new IvrServiceLog();
            $ivrServiceLog
                ->setCallId($callId)
                ->setAlias("İgdaş Menü Karşılama F")
                ->setInput($tdcID)
                ->setRequest($client->__getLastRequest())
                ->setResponse($client->__getLastResponse())
                ->setCreatesAt(new \DateTime());
            $em->persist($ivrServiceLog);
            $em->flush();

//            dump($invoiceDebtF);
//            exit();

            if ($invoiceDebtF->mtGetFaturaBorcFByTDCResult->_HukukHavaleEdilmis == true){

                if ($invoiceDebtF->mtGetFaturaBorcFByTDCResult->_TakipBaslatilmis == true){

                    $anons = "Sayın Abonemiz. Borcunuzdan dolayı icra takibi başlamıştır. ".$invoiceDebtF->mtGetFaturaBorcFByTDCResult->_GecenFaturaSayisi." adet faturanız bulunmaktadır. Ödenmesi gereken toplam bedeli ".$invoiceDebtF->mtGetFaturaBorcFByTDCResult->_GecenFaturaTutar." TL dir. Hukuk Birimi İle Görüşmek İçin ikiyi Tuşlayınız.";

                    $anons_2 = "Sayın Abonemiz. Ödemeniz gereken fatura borcunuz hakkında detaylı bilgi almak için. ".$invoiceDebtF->mtGetFaturaBorcFByTDCResult->_AvukatTelefon." numaralı telefondan  avukat ".$invoiceDebtF->mtGetFaturaBorcFByTDCResult->_Avukat." ile görüşebilirsiniz. Anonsu tekrar dinlemek için biri tuşlayınız.";

                    $anons_3 = "Sözleşme İşlemleri hakkında bilgi almak için üçü. Abonelik ve Sözleşme Şartları. Doğalgaz Birim Fiyatları. Fatura Ödeme Noktaları. İGDAŞ Hizmet Binaları Tesisatçı Firmalar ve Kampanyalar Hakkında Bilgi Almak için dördü. Müracaatınızın durumu hakkında bilgi almak için beşi. Ekonomik ve Verimli Doğalgaz Kullanımı hakkında bilgi almak için altıyı. Öneri ve Şikayetlerinizi Sesli Mesaj yoluyla bizlere iletmek için ikiyi. Müşteri Temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                    return $this->json([
                        "tdcID" => $tdcID,
                        "lawRegistration" => $lawRegistration->mtHukukKaydiVarmiResult->_cevap,
                        "law" => $invoiceDebtF->mtGetFaturaBorcFByTDCResult->_HukukHavaleEdilmis,
                        "follow" => $invoiceDebtF->mtGetFaturaBorcFByTDCResult->_TakipBaslatilmis,
                        "anons" => $anons,
                        "anons_2" => $anons_2,
                        "anons_3" => $anons_3,
                        "gross" => "",
                        "totalDebt" => "",
                        "totalGivingBack" => ""
                    ]);

                }else{

                    $anons = "Sayın Abonemiz. Fatura borçlarınızdan dolayı sözleşmeniz feshedilerek hesabınız hukuk birimine aktarılmıştır. ".$invoiceDebtF->mtGetFaturaBorcFByTDCResult->_GecenFaturaSayisi." adet faturanız bulunmaktadır. Ödenmesi gereken toplam bedel ".$invoiceDebtF->mtGetFaturaBorcFByTDCResult->_GecenFaturaTutar." TL dir. Bilgileri tekrar dinlemek için biri. Bir üst menüye dönmek için ikiyi. Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız";

                    $anons_2 = "";

                    $anons_3 = "Sözleşme İşlemleri hakkında bilgi almak için üçü. Abonelik ve Sözleşme Şartları. Doğalgaz Birim Fiyatları. Fatura Ödeme Noktaları. İGDAŞ Hizmet Binaları Tesisatçı Firmalar ve Kampanyalar Hakkında Bilgi Almak için dördü. Müracaatınızın durumu hakkında bilgi almak için beşi. Ekonomik ve Verimli Doğalgaz Kullanımı hakkında bilgi almak için altıyı. Öneri ve Şikayetlerinizi Sesli Mesaj yoluyla bizlere iletmek için ikiyi. Müşteri Temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                    return $this->json([
                        "tdcID" => $tdcID,
                        "lawRegistration" => $lawRegistration->mtHukukKaydiVarmiResult->_cevap,
                        "law" => $invoiceDebtF->mtGetFaturaBorcFByTDCResult->_HukukHavaleEdilmis,
                        "follow" => $invoiceDebtF->mtGetFaturaBorcFByTDCResult->_TakipBaslatilmis,
                        "anons" => $anons,
                        "anons_2" => $anons_2,
                        "anons_3" => $anons_3,
                        "gross" => "",
                        "totalDebt" => "",
                        "totalGivingBack" => ""
                    ]);

                }

            }else{

                $anons = "Sayın abonemiz. tesisatınıza ait ".$invoiceDebtF->mtGetFaturaBorcFByTDCResult->_GecenFaturaSayisi." adet. toplam ".$invoiceDebtF->mtGetFaturaBorcFByTDCResult->_GecenFaturaTutar." TL fatura borcunuz bulunmaktadır. Borçlarınızın ödenmemesi durumunda tesisatınızın bilgileri hukuk birimine aktarılacaktır. Bilgileri tekrar dinlemek için biri. Bir üst menüye dönmek için ikiyi. Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                $anons_2 = "";

                $anons_3 = "Sözleşme İşlemleri hakkında bilgi almak için üçü. Abonelik ve Sözleşme Şartları. Doğalgaz Birim Fiyatları. Fatura Ödeme Noktaları. İGDAŞ Hizmet Binaları Tesisatçı Firmalar ve Kampanyalar Hakkında Bilgi Almak için dördü. Müracaatınızın durumu hakkında bilgi almak için beşi. Ekonomik ve Verimli Doğalgaz Kullanımı hakkında bilgi almak için altıyı. Öneri ve Şikayetlerinizi Sesli Mesaj yoluyla bizlere iletmek için ikiyi. Müşteri Temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                return $this->json([
                    "tdcID" => $tdcID,
                    "lawRegistration" => $lawRegistration->mtHukukKaydiVarmiResult->_cevap,
                    "law" => $invoiceDebtF->mtGetFaturaBorcFByTDCResult->_HukukHavaleEdilmis,
                    "follow" => $invoiceDebtF->mtGetFaturaBorcFByTDCResult->_TakipBaslatilmis,
                    "anons" => $anons,
                    "anons_2" => $anons_2,
                    "anons_3" => $anons_3,
                    "gross" => "",
                    "totalDebt" => "",
                    "totalGivingBack" => ""
                ]);

            }

        }else{

            $assuranceF = $client->mtGetguvenceIade(['pSozlesmeHesapNo' => $tdcNo]);
            $ivrServiceLog = new IvrServiceLog();
            $ivrServiceLog
                ->setCallId($callId)
                ->setAlias("İgdaş Menü Karşılama F")
                ->setInput($tdcNo)
                ->setRequest($client->__getLastRequest())
                ->setResponse($client->__getLastResponse())
                ->setCreatesAt(new \DateTime());
            $em->persist($ivrServiceLog);
            $em->flush();

//            dump($assuranceF);
//            exit();

            if ($assuranceF->mtGetguvenceIadeResult->_cevap == true){

                if ($assuranceF->mtGetguvenceIadeResult->_BorcDurumu == "Alacakli"){

                    $alacak_bedeli = $assuranceF->mtGetguvenceIadeResult->_GuncelenenGuvence - $assuranceF->mtGetguvenceIadeResult->_MahsupEdilenGuvence;

                    $anons = "Sayın Abonemiz. Ödemiş olduğunuz toplam ".$assuranceF->mtGetguvenceIadeResult->_TahsilEdilenGuvence." TL Güvence bedeliniz. ".$assuranceF->mtGetguvenceIadeResult->_GuncelenenGuvence." TL olarak güncellenmiştir. Güncellenen güvence bedelinizden toplam ".$assuranceF->mtGetguvenceIadeResult->_ToplamBorcTutar." TL Kullanım bedeli borçlarınız düşülerek mahsuplaştırılmıştır. Mahsuplaşma sonucunda ".$assuranceF->mtGetguvenceIadeResult->_ToplamIadeTutari." TL Alacağınız bulunmaktadır. ".$alacak_bedeli." TL Güvence bedeli alacağınızı İGDAŞ Şubelerinden alabilirsiniz.";

                    $anons_2 = "";

                    $anons_3 = "Sözleşme İşlemleri hakkında bilgi almak için üçü. Abonelik ve Sözleşme Şartları. Doğalgaz Birim Fiyatları. Fatura Ödeme Noktaları. İGDAŞ Hizmet Binaları Tesisatçı Firmalar ve Kampanyalar Hakkında Bilgi Almak için dördü. Müracaatınızın durumu hakkında bilgi almak için beşi. Ekonomik ve Verimli Doğalgaz Kullanımı hakkında bilgi almak için altıyı. Öneri ve Şikayetlerinizi Sesli Mesaj yoluyla bizlere iletmek için ikiyi. Müşteri Temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                    return $this->json([
                        "tdcID" => $tdcID,
                        "lawRegistration" => $lawRegistration->mtHukukKaydiVarmiResult->_cevap,
                        "law" => "",
                        "follow" => "",
                        "anons" => $anons,
                        "anons_2" => $anons_2,
                        "anons_3" => $anons_3,
                        "gross" => $assuranceF->mtGetguvenceIadeResult->_cevap,
                        "totalDebt" => $assuranceF->mtGetguvenceIadeResult->_ToplamBorcTutar,
                        "totalGivingBack" => 1
                    ]);

                }else{

                    if ($assuranceF->mtGetguvenceIadeResult->_BorcDurumu == "Borclu"){

                        $anons = "Sayın Abonemiz. Ödemiş olduğunuz toplam ".$assuranceF->mtGetguvenceIadeResult->_TahsilEdilenGuvence." TL Güvence bedeliniz. ".$assuranceF->mtGetguvenceIadeResult->_GuncelenenGuvence." TL olarak güncellenmiştir. Güncellenen güvence bedelinizden toplam ".$assuranceF->mtGetguvenceIadeResult->_ToplamBorcTutar." TL Kullanım bedeli borçlarınız düşülerek mahsuplaştırılmıştır. Mahsuplaşma sonucunda ".$assuranceF->mtGetguvenceIadeResult->_MahsupEdilenGuvence." TL Borcunuz bulunmaktadır. Bir hafta için de borçlarınız Hukuk birimine aktarılacaktır. Borcunuzu en yakın İGDAŞ veznelerinden ödeyebilirsiniz.";

                        $anons_2 = "";

                        $anons_3 = "Sözleşme İşlemleri hakkında bilgi almak için üçü. Abonelik ve Sözleşme Şartları. Doğalgaz Birim Fiyatları. Fatura Ödeme Noktaları. İGDAŞ Hizmet Binaları Tesisatçı Firmalar ve Kampanyalar Hakkında Bilgi Almak için dördü. Müracaatınızın durumu hakkında bilgi almak için beşi. Ekonomik ve Verimli Doğalgaz Kullanımı hakkında bilgi almak için altıyı. Öneri ve Şikayetlerinizi Sesli Mesaj yoluyla bizlere iletmek için ikiyi. Müşteri Temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                        return $this->json([
                            "tdcID" => $tdcID,
                            "lawRegistration" => $lawRegistration->mtHukukKaydiVarmiResult->_cevap,
                            "law" => "",
                            "follow" => "",
                            "anons" => $anons,
                            "anons_2" => $anons_2,
                            "anons_3" => $anons_3,
                            "gross" => $assuranceF->mtGetguvenceIadeResult->_cevap,
                            "totalDebt" => $assuranceF->mtGetguvenceIadeResult->_ToplamBorcTutar,
                            "totalGivingBack" => 1
                        ]);

                    }

                }

            }else{

                $anons = "Sayın Abonemiz. Ödemiş olduğunuz Güvence bedellerinin güncellenmesi ve Kullanım bedeli borçlarınızdan mahsuplaştırılması işlemi henüz sonuçlanmamıştır. Bu işlem sonuçlandığında tarafınıza SMS ile bilgi verilecektir. Mahsuplaşma işleminiz hakkında detaylı bilgi almak için biri tuşlayınız.";

                $anons_2 = "Güvence bedeli mahsuplaşma detaylı bilgi talebiniz alınmıştır. en kısa zamanda size geri dönüş yapılacaktır.";

                $anons_3 = "Sözleşme İşlemleri hakkında bilgi almak için üçü. Abonelik ve Sözleşme Şartları. Doğalgaz Birim Fiyatları. Fatura Ödeme Noktaları. İGDAŞ Hizmet Binaları Tesisatçı Firmalar ve Kampanyalar Hakkında Bilgi Almak için dördü. Müracaatınızın durumu hakkında bilgi almak için beşi. Ekonomik ve Verimli Doğalgaz Kullanımı hakkında bilgi almak için altıyı. Öneri ve Şikayetlerinizi Sesli Mesaj yoluyla bizlere iletmek için ikiyi. Müşteri Temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                return $this->json([
                    "tdcID" => $tdcID,
                    "lawRegistration" => $lawRegistration->mtHukukKaydiVarmiResult->_cevap,
                    "law" => "",
                    "follow" => "",
                    "anons" => $anons,
                    "anons_2" => $anons_2,
                    "anons_3" => $anons_3,
                    "gross" => $assuranceF->mtGetguvenceIadeResult->_cevap,
                    "totalDebt" => $assuranceF->mtGetguvenceIadeResult->_ToplamBorcTutar,
                    "totalGivingBack" => 0
                ]);

            }

        }

    }

}