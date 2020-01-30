<?php

namespace App\IVR\IgdasBundle\Controller;

use App\Entity\IvrServiceLog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WelcomeMenuKAndLController extends AbstractController
{

    /**
     * @Route("/ivr/igdas/pbx/welcomeMenuKAndL/invoiceDebt/{callId}/{tdcID}")
     * @param Request $request
     * @param $callId
     * @param $tdcID
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function welcomeMenuKAndLinvoiceDebt(Request $request, $callId, $tdcID)
    {

        $client = new \SoapClient($this->getParameter('igdasApiLink'), array("trace" => 1));
        $em = $this->getDoctrine()->getManager();

        $tdcNo = $client->mtGetTesisatByTDC(['pTDCno' => $tdcID]);
        $ivrServiceLog = new IvrServiceLog();
        $ivrServiceLog
            ->setCallId($callId)
            ->setAlias("İgdaş Menü Karşılama K-L")
            ->setInput($tdcID)
            ->setRequest($client->__getLastRequest())
            ->setResponse($client->__getLastResponse())
            ->setCreatesAt(new \DateTime());
        $em->persist($ivrServiceLog);
        $em->flush();

        $lawRegistration = $client->mtHukukKaydiVarmi(['pSozlesmeHesapNo' => $tdcID]);
//        $lawRegistration = $lawRegistration->mtHukukKaydiVarmiResult->_cevap;
        $ivrServiceLog = new IvrServiceLog();
        $ivrServiceLog
            ->setCallId($callId)
            ->setAlias("İgdaş Menü Karşılama K-L")
            ->setInput($tdcID)
            ->setRequest($client->__getLastRequest())
            ->setResponse($client->__getLastResponse())
            ->setCreatesAt(new \DateTime());
        $em->persist($ivrServiceLog);
        $em->flush();


        // TDCID Değişim Yeri
        $tdcID = $tdcNo->mtGetTesisatByTDCResult->_TDCID;



//        $invoiceDebt = $client->mtGetFaturaBorcKLByTDC(['pTDCID' => $tdcID]);
        $invoiceDebt = $client->mtGetFaturaBorcKLByTDC(['pTDCID' => $tdcID]);
        $ivrServiceLog = new IvrServiceLog();
        $ivrServiceLog
            ->setCallId($callId)
            ->setAlias("İgdaş Menü Karşılama K-L")
            ->setInput($tdcID)
            ->setRequest($client->__getLastRequest())
            ->setResponse($client->__getLastResponse())
            ->setCreatesAt(new \DateTime());
        $em->persist($ivrServiceLog);
        $em->flush();

//        dump($lawRegistration);
//        exit();

        //        Hukuk kaydı warmı bize böyle bir çıkış vermiyor. Değiştirdim.
        if ($lawRegistration->mtHukukKaydiVarmiResult->_cevap == true){

            return $this->json([
                "tdcID" => $tdcID,
                "anons" => "",
                "anons_2" => "",
                "lawRegistration" => $lawRegistration->mtHukukKaydiVarmiResult->_cevap,
                "numberOfBillsPassed" => $invoiceDebt->mtGetFaturaBorcKLByTDCResult->_FaturaAdedi,
                "workOrder" => false
            ]);

        }else{

            if ($invoiceDebt->mtGetFaturaBorcKLByTDCResult->_FaturaAdedi > 0){

                $anons = "Sayın abonemiz. tesisatınız Son ödeme tarihi geçmiş ".$invoiceDebt->mtGetFaturaBorcKLByTDCResult->_FaturaAdedi." adet toplam ".$invoiceDebt->mtGetFaturaBorcKLByTDCResult->_FaturaBorc." TL fatura borcunuzdan dolayı kullanıma kapatılmıştır.  Gazınızın açılması için gecikmeli tüm fatura borçlarınızın ödenmesi gerekmektedir. Gecikmeli tüm borçların ödenmesini takip eden 48 saat içerisinde tekrar gaz açma işlemi gerçekleştirilir.";


                $anons_2 = "Sözleşme Fesih İşlemleri hakkında bilgi almak için biri. Abonelik ve Sözleşme Şartları. Doğalgaz Birim Fiyatları. Fatura Ödeme Noktaları. İGDAŞ Hizmet Binaları. Tesisatçı Firmalar ve Kampanyalar Hakkında Bilgi Almak için ikiyi. Daha Ekonomik ve Verimli Doğalgaz Kullanımı hakkında bilgi almak için üçü. Müracaatınızın durumu hakkında bilgi almak için dördü. Öneri ve Şikayetlerinizi Sesli Mesaj yoluyla bizlere iletmek için beşi. Müşteri Temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                return $this->json([
                    "tdcID" => $tdcID,
                    "anons" => $anons,
                    "anons_2" => $anons_2,
                    "lawRegistration" => $lawRegistration->mtHukukKaydiVarmiResult->_cevap,
                    "numberOfBillsPassed" => $invoiceDebt->mtGetFaturaBorcKLByTDCResult->_FaturaAdedi,
                    "workOrder" => false
                ]);

            }else{

                $workOrder = $client->mtGetIsEmriByTDC(['pTDCID' => $tdcID]);
                $ivrServiceLog = new IvrServiceLog();
                $ivrServiceLog
                    ->setCallId($callId)
                    ->setAlias("İgdaş Menü Karşılama K-L")
                    ->setInput($tdcID)
                    ->setRequest($client->__getLastRequest())
                    ->setResponse($client->__getLastResponse())
                    ->setCreatesAt(new \DateTime());
                $em->persist($ivrServiceLog);
                $em->flush();

                if ($workOrder->mtGetIsEmriByTDCResult->_IsEmriVar == true){

                    $anons = "Fatura ödemeleriniz sistemimize işlemiştir. Tesisatınız gaz açma programına alınmıştır.  Yasal Süre 48 saat içerisinde gaz arzınız sağlanacaktır.";

                    $anons_2 = "Sözleşme Fesih İşlemleri hakkında bilgi almak için biri. Abonelik ve Sözleşme Şartları. Doğalgaz Birim Fiyatları. Fatura Ödeme Noktaları. İGDAŞ Hizmet Binaları. Tesisatçı Firmalar ve Kampanyalar Hakkında Bilgi Almak için ikiyi. Daha Ekonomik ve Verimli Doğalgaz Kullanımı hakkında bilgi almak için üçü. Müracaatınızın durumu hakkında bilgi almak için dördü. Öneri ve Şikayetlerinizi Sesli Mesaj yoluyla bizlere iletmek için beşi. Müşteri Temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                    return $this->json([
                        "tdcID" => $tdcID,
                        "anons" => $anons,
                        "anons_2" => $anons_2,
                        "lawRegistration" => $lawRegistration->mtHukukKaydiVarmiResult->_cevap,
                        "numberOfBillsPassed" => $invoiceDebt->mtGetFaturaBorcKLByTDCResult->_FaturaAdedi,
                        "workOrder" => $workOrder->mtGetIsEmriByTDCResult->_IsEmriVar
                    ]);

                }else{

                    $anons = "Fatura ödemeleriniz sistemimize işlemiştir.  Tesisatınızın gaz açma işlemi en kısa sürede gerçekleştirilecektir.";

                    $anons_2 = "Sözleşme Fesih İşlemleri hakkında bilgi almak için biri. Abonelik ve Sözleşme Şartları. Doğalgaz Birim Fiyatları. Fatura Ödeme Noktaları. İGDAŞ Hizmet Binaları. Tesisatçı Firmalar ve Kampanyalar Hakkında Bilgi Almak için ikiyi. Daha Ekonomik ve Verimli Doğalgaz Kullanımı hakkında bilgi almak için üçü. Müracaatınızın durumu hakkında bilgi almak için dördü. Öneri ve Şikayetlerinizi Sesli Mesaj yoluyla bizlere iletmek için beşi. Müşteri Temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                    return $this->json([
                        "tdcID" => $tdcID,
                        "anons" => $anons,
                        "anons_2" => $anons_2,
                        "lawRegistration" => $lawRegistration->mtHukukKaydiVarmiResult->_cevap,
                        "numberOfBillsPassed" => $invoiceDebt->mtGetFaturaBorcKLByTDCResult->_FaturaAdedi,
                        "workOrder" => $workOrder->mtGetIsEmriByTDCResult->_IsEmriVar
                    ]);

                }

            }

        }

    }
}