<?php

namespace App\IVR\IgdasBundle\Controller;

use App\Entity\IvrServiceLog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TerminationAndAssuranceInformationController extends AbstractController
{

    /**
     * @Route("/ivr/igdas/pbx/terminationAndAssuranceInformation/{callId}/{tdcNo}")
     * @param Request $request
     * @param $tdcNo
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function terminationAndAssuranceInformation(Request $request, $callId, $tdcNo)
    {
        $client = new \SoapClient($this->getParameter('igdasApiLink'), array("trace" => 1));
        $em = $this->getDoctrine()->getManager();

        $tdcID = $client->mtGetTesisatByTDC(['pTDCno' => $tdcNo]);
        $ivrServiceLog = new IvrServiceLog();
        $ivrServiceLog
            ->setCallId($callId)
            ->setAlias("İgdaş Menü Fesih ve Güvence İade Bilgileri")
            ->setInput($tdcNo)
            ->setRequest($client->__getLastRequest())
            ->setResponse($client->__getLastResponse())
            ->setCreatesAt(new \DateTime());
        $em->persist($ivrServiceLog);
        $em->flush();

        $tdcID = $tdcID->mtGetTesisatByTDCResult->_TDCID;

        $terminationApplication = $client->mtgetFesihDurumu(['pSozlesmeHesapNo' => $tdcNo]);
        $ivrServiceLog = new IvrServiceLog();
        $ivrServiceLog
            ->setCallId($callId)
            ->setAlias("İgdaş Menü Fesih ve Güvence İade Bilgileri")
            ->setInput($tdcNo)
            ->setRequest($client->__getLastRequest())
            ->setResponse($client->__getLastResponse())
            ->setCreatesAt(new \DateTime());
        $em->persist($ivrServiceLog);
        $em->flush();

//        dump($terminationApplication);
//        exit();


        if ($terminationApplication->mtgetFesihDurumuResult->_FesihBasvurusuVarMi == 0){

            $anons = "Sayın abonemiz sözleşme fesih işleminin. abonenin doğalgaz kullanımı yaptığı mahalden taşınması veya doğalgaz kullanımından vazgeçmesi durumunda yapılması gerekmektedir. Fesih müracaatının ardından sayaç kullanıma kapatılarak hesap tasfiyesi yapılmaktadır.";

            $debt = $client->mtGetBorcbyTDC(['pTDCID' => $tdcID]);
            $ivrServiceLog = new IvrServiceLog();
            $ivrServiceLog
                ->setCallId($callId)
                ->setAlias("İgdaş Menü Fesih ve Güvence İade Bilgileri")
                ->setInput($tdcID)
                ->setRequest($client->__getLastRequest())
                ->setResponse($client->__getLastResponse())
                ->setCreatesAt(new \DateTime());
            $em->persist($ivrServiceLog);
            $em->flush();

//            dump($debt);
//            exit();

            if ($debt->mtGetBorcbyTDCResult->_BorcVar == true){
                if (isset($debt->mtGetBorcbyTDCResult->_Banka)){
                    $anons_2 = "Sayın abonemiz. sözleşmenizi feshetmeden önce fatura borcunun tamamının ödenmesi gerekmektedir. Lütfen. bu tesisat için vermiş olduğunuz ".$debt->mtGetBorcbyTDCResult->_Banka." Bankasında ki otomatik ödeme talimatınızı iptal ettirmeyi unutmayınız";
                    if ($debt->mtGetBorcbyTDCResult->_EtkinlikKodu == 10 and in_array($debt->mtGetBorcbyTDCResult->_KullanimSinifi,[1,2,3,5,6,16])){
                        $anons_3 = "İşlem kanallarını öğrenmek için biri. Fesih esnasında istenilen belgeleri öğrenmek için ikiyi. Güvence bedeli iadesi hakkında bilgi almak için üçü. Bir üst menüye dönmek için dördü. Müşteri temsilcisine bağlanmak için lütfen sıfırı. tuşlayınız";
                        return $this->json([
                            "anons" => $anons,
                            "anons_2" => $anons_2,
                            "anons_3" => $anons_3,
                            "debt" => $debt->mtGetBorcbyTDCResult->_BorcVar,
                            "bank" => 1,
                            "code" => 1,
                            "application" => 0
                        ]);
                    }elseif (in_array($debt->mtGetBorcbyTDCResult->_EtkinlikKodu,[10,12]) and in_array($debt->mtGetBorcbyTDCResult->_KullanimSinifi,[4,11])){
                        $anons_3 = "Sözleşme fesih işleminizi size en yakın İGDAŞ hizmet binamıza gelerek yaptırabilirsiniz. Sizden istenilen belgeleri öğrenmek için ikiyi. Güvence bedeli iadesi hakkında bilgi almak için üçü. Ana menüye dönmek için dördü. Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";
                        return $this->json([
                            "anons" => $anons,
                            "anons_2" => $anons_2,
                            "anons_3" => $anons_3,
                            "debt" => $debt->mtGetBorcbyTDCResult->_BorcVar,
                            "bank" => 1,
                            "code" => 2,
                            "application" => 0
                        ]);
                    }elseif (in_array($debt->mtGetBorcbyTDCResult->_EtkinlikKodu,[20,22,25,40,50,55,60,70,75,91,92])){
                        $anons_3 = "Sözleşme fesih işleminizi size en yakın İGDAŞ hizmet binamıza gelerek yaptırabilirsiniz. Sizden istenilen belgeleri öğrenmek için ikiyi. Güvence bedeli iadesi hakkında bilgi almak için üçü. Ana menüye dönmek için dördü. Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";
                        return $this->json([
                            "anons" => $anons,
                            "anons_2" => $anons_2,
                            "anons_3" => $anons_3,
                            "debt" => $debt->mtGetBorcbyTDCResult->_BorcVar,
                            "bank" => 1,
                            "code" => 3,
                            "application" => 0
                        ]);
                    }
                }elseif (!isset($debt->mtGetBorcbyTDCResult->_Banka)){
                    $anons_2 = "Sayın abonemiz. sözleşmenizi feshetmeden önce fatura borcunun tamamının ödenmesi gerekmektedir";
                    if ($debt->mtGetBorcbyTDCResult->_EtkinlikKodu == 10 and in_array($debt->mtGetBorcbyTDCResult->_KullanimSinifi,[1,2,3,5,6,16])){
                        $anons_3 = "İşlem kanallarını öğrenmek için biri. Fesih esnasında istenilen belgeleri öğrenmek için ikiyi. Güvence bedeli iadesi hakkında bilgi almak için üçü. Bir üst menüye dönmek için dördü. Müşteri temsilcisine bağlanmak için lütfen sıfırı. tuşlayınız";
                        return $this->json([
                            "anons" => $anons,
                            "anons_2" => $anons_2,
                            "anons_3" => $anons_3,
                            "debt" => $debt->mtGetBorcbyTDCResult->_BorcVar,
                            "bank" => 0,
                            "code" => 1,
                            "application" => 0
                        ]);
                    }elseif (in_array($debt->mtGetBorcbyTDCResult->_EtkinlikKodu,[10,12]) and in_array($debt->mtGetBorcbyTDCResult->_KullanimSinifi,[4,11])){
                        $anons_3 = "Sözleşme fesih işleminizi size en yakın İGDAŞ hizmet binamıza gelerek yaptırabilirsiniz. Sizden istenilen belgeleri öğrenmek için ikiyi. Güvence bedeli iadesi hakkında bilgi almak için üçü. Ana menüye dönmek için dördü. Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";
                        return $this->json([
                            "anons" => $anons,
                            "anons_2" => $anons_2,
                            "anons_3" => $anons_3,
                            "debt" => $debt->mtGetBorcbyTDCResult->_BorcVar,
                            "bank" => 0,
                            "code" => 2,
                            "application" => 0
                        ]);
                    }elseif (in_array($debt->mtGetBorcbyTDCResult->_EtkinlikKodu,[20,22,25,40,50,55,60,70,75,91,92])){
                        $anons_3 = "Sözleşme fesih işleminizi size en yakın İGDAŞ hizmet binamıza gelerek yaptırabilirsiniz. Sizden istenilen belgeleri öğrenmek için ikiyi. Güvence bedeli iadesi hakkında bilgi almak için üçü. Ana menüye dönmek için dördü. Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";
                        return $this->json([
                            "anons" => $anons,
                            "anons_2" => $anons_2,
                            "anons_3" => $anons_3,
                            "debt" => $debt->mtGetBorcbyTDCResult->_BorcVar,
                            "bank" => 0,
                            "code" => 3,
                            "application" => 0
                        ]);
                    }
                }
            }elseif ($debt->mtGetBorcbyTDCResult->_BorcVar == false){
                if (isset($debt->mtGetBorcbyTDCResult->_Banka)){
                    $anons_2 = "Sayın abonemiz. lütfen bu tesisat için vermiş olduğunuz ".$debt->mtGetBorcbyTDCResult->_Banka." Bankasında ki otomatik ödeme talimatınızı iptal ettirmeyi unutmayınız";
                    if ($debt->mtGetBorcbyTDCResult->_EtkinlikKodu == 10 and in_array($debt->mtGetBorcbyTDCResult->_KullanimSinifi,[1,2,3,5,6,16])){
                        $anons_3 = "İşlem kanallarını öğrenmek için biri. Fesih esnasında istenilen belgeleri öğrenmek için ikiyi. Güvence bedeli iadesi hakkında bilgi almak için üçü. Bir üst menüye dönmek için dördü. Müşteri temsilcisine bağlanmak için lütfen sıfırı. tuşlayınız";
                        return $this->json([
                            "anons" => $anons,
                            "anons_2" => $anons_2,
                            "anons_3" => $anons_3,
                            "debt" => $debt->mtGetBorcbyTDCResult->_BorcVar,
                            "bank" => 1,
                            "code" => 1,
                            "application" => 0
                        ]);
                    }elseif (in_array($debt->mtGetBorcbyTDCResult->_EtkinlikKodu,[10,12]) and in_array($debt->mtGetBorcbyTDCResult->_KullanimSinifi,[4,11])){
                        $anons_3 = "Sözleşme fesih işleminizi size en yakın İGDAŞ hizmet binamıza gelerek yaptırabilirsiniz. Sizden istenilen belgeleri öğrenmek için ikiyi. Güvence bedeli iadesi hakkında bilgi almak için üçü. Ana menüye dönmek için dördü. Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";
                        return $this->json([
                            "anons" => $anons,
                            "anons_2" => $anons_2,
                            "anons_3" => $anons_3,
                            "debt" => $debt->mtGetBorcbyTDCResult->_BorcVar,
                            "bank" => 1,
                            "code" => 2,
                            "application" => 0
                        ]);
                    }elseif (in_array($debt->mtGetBorcbyTDCResult->_EtkinlikKodu,[20,22,25,40,50,55,60,70,75,91,92])){
                        $anons_3 = "Sözleşme fesih işleminizi size en yakın İGDAŞ hizmet binamıza gelerek yaptırabilirsiniz. Sizden istenilen belgeleri öğrenmek için ikiyi. Güvence bedeli iadesi hakkında bilgi almak için üçü. Ana menüye dönmek için dördü. Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";
                        return $this->json([
                            "anons" => $anons,
                            "anons_2" => $anons_2,
                            "anons_3" => $anons_3,
                            "debt" => $debt->mtGetBorcbyTDCResult->_BorcVar,
                            "bank" => 1,
                            "code" => 3,
                            "application" => 0
                        ]);
                    }
                }else{
                    return $this->json([
                        "anons" => "",
                        "anons_2" => "",
                        "anons_3" => "",
                        "debt" => "",
                        "bank" => "",
                        "code" => "",
                        "application" => ""
                    ]);
                }
            }

        }else{
            $anons = "Sayın abonemiz. Fesih müracaatınız ".date('d-m-Y',strtotime($terminationApplication->mtgetFesihDurumuResult->_PlanlananFesihTarihi))." tarihinde sayacınız mühürlenerek yapılacaktır. Fesih tarihini değiştirmek için biri. Başa dönmek için lütfen ikiyi tuşlayınız.";
            return $this->json([
                "anons" => $anons,
                "anons_2" => "",
                "anons_3" => "",
                "debt" => "",
                "bank" => "",
                "code" => "",
                "application" => 1
            ]);
        }

    }
}