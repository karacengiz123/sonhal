<?php

namespace App\IVR\IgdasBundle\Controller;

use phpDocumentor\Reflection\Types\This;
use App\Entity\IvrServiceLog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WelcomeController extends AbstractController
{

    /**
     * @Route("/ivr/igdas/pbx/welcome/checkGsmNumber/{callId}/{gsm}")
     * @param Request $request
     * @param $callId
     * @param $gsm
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function ws001(Request $request, $callId, $gsm)
    {
        $client = new \SoapClient($this->getParameter('igdasApiLink'), array("trace" => 1));
        $em = $this->getDoctrine()->getManager();

        $customer = $client->mtGetTesisatByTelefon(['pTelefonNo' => $gsm]);
        $ivrServiceLog = new IvrServiceLog();
        $ivrServiceLog
            ->setCallId($callId)
            ->setAlias("İgdaş Menü Karşılama Akışı - Gsm Kontrol")
            ->setInput($gsm)
            ->setRequest($client->__getLastRequest())
            ->setResponse($client->__getLastResponse())
            ->setCreatesAt(new \DateTime());
        $em->persist($ivrServiceLog);
        $em->flush();

        $kayit_sayisi = $customer->mtGetTesisatByTelefonResult->_KayitSayisi;

//        dump($customer);
//        exit();

        if ($kayit_sayisi == 0){
            return $this->json([
                "kayitSayisi" => $kayit_sayisi,
                "soyAdi" => "",
                "tesisatNo" => "",
                "daireNo" => "",
                "cariNo" => "",
                "aDurumu" => "",
                "anons" => "",
                "tdcNo" => ""
            ]);
        }else{
            $fors = $customer->mtGetTesisatByTelefonResult->_TesisatRecords->TesisatInfo;
            if (is_array($fors)){
                $sozlesmeHesapNo = "";
                $i = 1;
                $tdcID = array();
                foreach ($fors as $for){
//                    ${"tdcID_".$i} = $for->_TesisatNo;
                    $tdcID["tdcID_".$i]= $for->_TDCID;
                    $tdcNo["tdcNo_".$i]= $for->_SozlesmeHesapNo;

                    $s_h_n = $for->_SozlesmeHesapNo;

                    $s_h_n_1 = substr($s_h_n,0,3);
                    $s_h_n_2 = substr($s_h_n,3,3);
                    $s_h_n_3 = substr($s_h_n,6,3);
                    $s_h_n_4 = substr($s_h_n,9,3);

                    $s_h_n = "".$s_h_n_1." . ".$s_h_n_2." . ".$s_h_n_3." . ".$s_h_n_4." . ";

                    $sozlesmeHesapNo.= $s_h_n." numaralı sözleşmeniz ile ilgili işlem yapmak veya bilgi almak için $i . ";
                    $i++;
                }
                $anons = "Sayın ".$customer->mtGetTesisatByTelefonResult->_TesisatRecords->TesisatInfo[0]->_Soyadi.". adınıza kayıtlı ".$kayit_sayisi." adet Sözleşme Hesap numarası bulunuyor. ".$sozlesmeHesapNo." Başka bir Sözleşme Hesap numarası ile işlem yapmak veya bilgi almak için dokuzu. Kazı işlemleri. gaz yokluğu ve ihbar kaydı bırakmak için sıfırı. Sözleşme Hesap numaralarını tekrar dinlemek için ikiyi tuşlayınız.";

                return $this->json([
                    "kayitSayisi" => $kayit_sayisi,
                    "soyAdi" => $customer->mtGetTesisatByTelefonResult->_TesisatRecords->TesisatInfo[0]->_Soyadi,
                    "tesisatNo" => $customer->mtGetTesisatByTelefonResult->_TesisatRecords->TesisatInfo[0]->_TesisatNo,
                    "daireNo" => $customer->mtGetTesisatByTelefonResult->_TesisatRecords->TesisatInfo[0]->_DaireNo,
                    "cariNo" => $customer->mtGetTesisatByTelefonResult->_TesisatRecords->TesisatInfo[0]->_CariNo,
                    "aDurumu" => $customer->mtGetTesisatByTelefonResult->_TesisatRecords->TesisatInfo[0]->_Adurumu,
                    "anons" => $anons,
                    "tdcNo" => $tdcNo,
                    "tdcID" => $tdcID
                ]);

            }else{

                $s_h_n = $customer->mtGetTesisatByTelefonResult->_TesisatRecords->TesisatInfo->_SozlesmeHesapNo;

                $s_h_n_1 = substr($s_h_n,0,3);
                $s_h_n_2 = substr($s_h_n,3,3);
                $s_h_n_3 = substr($s_h_n,6,3);
                $s_h_n_4 = substr($s_h_n,9,3);

                $s_h_n = "".$s_h_n_1." . ".$s_h_n_2." . ".$s_h_n_3." . ".$s_h_n_4." . ";

                $anons = "Sayın ".$customer->mtGetTesisatByTelefonResult->_TesisatRecords->TesisatInfo->_Soyadi.". adınıza kayıtlı ".$kayit_sayisi." adet Sözleşme Hesap numarası bulunuyor. ".$s_h_n." numaralı sözleşmeniz ile ilgili işlem yapmak veya bilgi almak için biri. Başka bir Sözleşme Hesap numarası ile işlem yapmak veya bilgi almak için dokuzu. Kazı işlemleri. gaz yokluğu ve ihbar kaydı bırakmak için sıfırı. Sözleşme Hesap numaralarını tekrar dinlemek için ikiyi tuşlayınız.";

                return $this->json([
                    "kayitSayisi" => $kayit_sayisi,
                    "soyAdi" => $customer->mtGetTesisatByTelefonResult->_TesisatRecords->TesisatInfo->_Soyadi,
                    "tesisatNo" => $customer->mtGetTesisatByTelefonResult->_TesisatRecords->TesisatInfo->_TesisatNo,
                    "daireNo" => $customer->mtGetTesisatByTelefonResult->_TesisatRecords->TesisatInfo->_DaireNo,
                    "cariNo" => $customer->mtGetTesisatByTelefonResult->_TesisatRecords->TesisatInfo->_CariNo,
                    "aDurumu" => $customer->mtGetTesisatByTelefonResult->_TesisatRecords->TesisatInfo->_Adurumu,
                    "anons" => $anons,
                    "tdcNo" => ["tdcNo_1" => $customer->mtGetTesisatByTelefonResult->_TesisatRecords->TesisatInfo->_SozlesmeHesapNo],
                    "tdcID" => ["tdcID_1" => $customer->mtGetTesisatByTelefonResult->_TesisatRecords->TesisatInfo->_TDCID]
                ]);

            }

        }

    }

    /**
     * @Route("/ivr/igdas/pbx/welcome/tdc/{callId}/{tdcNo}")
     * @param Request $request
     * @param $callId
     * @param $tdcNo
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function ws002(Request $request, $callId, $tdcNo)
    {
        $client = new \SoapClient($this->getParameter('igdasApiLink'), array("trace" => 1));
        $em = $this->getDoctrine()->getManager();

        $customer = $client->mtGetTesisatByTDC(['pTDCno' => $tdcNo]);
        $ivrServiceLog = new IvrServiceLog();
        $ivrServiceLog
            ->setCallId($callId)
            ->setAlias("İgdaş Menü Karşılama Akışı - Tesisat Kontrol")
            ->setInput($tdcNo)
            ->setRequest($client->__getLastRequest())
            ->setResponse($client->__getLastResponse())
            ->setCreatesAt(new \DateTime());
        $em->persist($ivrServiceLog);
        $em->flush();

        $kayit_sayisi = $customer->mtGetTesisatByTDCResult->_KayitSayisi;

//        dump($customer);
//        exit();

        if ($kayit_sayisi == 0){
            return $this->json(["tdcID" => "", "anons" => "0"]);
        }else{
            $tdcID = $customer->mtGetTesisatByTDCResult->_TDCID;
            $tdcNo = $customer->mtGetTesisatByTDCResult->_SozlesmeHesapNo;
            $aDurumu = $customer->mtGetTesisatByTDCResult->_Adurumu;

            $anons = "Sayın ".$customer->mtGetTesisatByTDCResult->_Soyadi.". Hoşgeldiniz. ";

            return $this->json(["tdcID" => $tdcID , 'anons' => $anons, "aDurumu" => $aDurumu, "tdcNo" => $tdcNo]);
        }
    }

    /**
     * @Route("/ivr/igdas/pbx/welcome/tc/{callId}/{tcNo}")
     * @param Request $request
     * @param $callId
     * @param $tcNo
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function ws003(Request $request, $callId, $tcNo)
    {
        $client = new \SoapClient($this->getParameter('igdasApiLink'), array("trace" => 1));
        $em = $this->getDoctrine()->getManager();

        $customer = $client->mtGetTesisatByTCno(['pTCno' => $tcNo]);
        $ivrServiceLog = new IvrServiceLog();
        $ivrServiceLog
            ->setCallId($callId)
            ->setAlias("İgdaş Menü Karşılama Akışı - Tc Kontrol")
            ->setInput($tcNo)
            ->setRequest($client->__getLastRequest())
            ->setResponse($client->__getLastResponse())
            ->setCreatesAt(new \DateTime());
        $em->persist($ivrServiceLog);
        $em->flush();

//        dump($customer);
//        exit();

        $kayit_sayisi = $customer->mtGetTesisatByTCnoResult->_KayitSayisi;

        if ($kayit_sayisi == 0){
            return $this->json(["tdcID" => "", "anons" => "0"]);
        }else{

            $fors = $customer->mtGetTesisatByTCnoResult->_TesisatRecords->TesisatInfo;
            if (is_array($fors)){
                $sozlesmeHesapNo = "";
                $i = 1;
                $tdcID = array();
                foreach ($fors as $for){
//                    ${"tdcID_".$i} = $for->_TesisatNo;
                    $aDurumu["aDurumu_".$i]= $for->_Adurumu;
                    $tdcID["tdcID_".$i]= $for->_TDCID;
                    $tdcNo["tdcNo_".$i]= $for->_SozlesmeHesapNo;

                    $s_h_n = $for->_SozlesmeHesapNo;

                    $s_h_n_1 = substr($s_h_n,0,3);
                    $s_h_n_2 = substr($s_h_n,3,3);
                    $s_h_n_3 = substr($s_h_n,6,3);
                    $s_h_n_4 = substr($s_h_n,9,3);

                    $s_h_n = "".$s_h_n_1." . ".$s_h_n_2." . ".$s_h_n_3." . ".$s_h_n_4." . ";

                    $sozlesmeHesapNo.= $s_h_n." numaları sözleşmeniz ile ilgili işlem yapmak veya bilgi almak için $i . ";
                    $i++;
                }
                $anons = "Sayın ".$customer->mtGetTesisatByTCnoResult->_TesisatRecords->TesisatInfo[0]->_Soyadi.". adınıza kayıtlı ".$customer->mtGetTesisatByTCnoResult->_KayitSayisi." adet Sözleşme Hesap numarası bulunuyor. ".$sozlesmeHesapNo." Başka bir Sözleşme Hesap numarası ile işlem yapmak veya bilgi almak için dokuzu. Sözleşme Hesap numaralarını tekrar dinlemek için ikiyi tuşlayınız.";

                return $this->json(["tdcID" => $tdcID, "aDurumu" => $aDurumu, "anons" => $anons, "kayitSayisi" => $kayit_sayisi, "tdcNo" => $tdcNo]);
            }else{
                $tdcID = $customer->mtGetTesisatByTCnoResult->_TesisatRecords->TesisatInfo->_TDCID;
                $aDurumu = $customer->mtGetTesisatByTCnoResult->_TesisatRecords->TesisatInfo->_Adurumu;
                $tdcNo = $customer->mtGetTesisatByTCnoResult->_TesisatRecords->TesisatInfo->_SozlesmeHesapNo;


                $s_h_n = $customer->mtGetTesisatByTCnoResult->_TesisatRecords->TesisatInfo->_SozlesmeHesapNo;

                $s_h_n_1 = substr($s_h_n,0,3);
                $s_h_n_2 = substr($s_h_n,3,3);
                $s_h_n_3 = substr($s_h_n,6,3);
                $s_h_n_4 = substr($s_h_n,9,3);

                $s_h_n = "".$s_h_n_1." . ".$s_h_n_2." . ".$s_h_n_3." . ".$s_h_n_4." . ";

                $anons = "Sayın ".$customer->mtGetTesisatByTCnoResult->_TesisatRecords->TesisatInfo->_Soyadi.". adınıza kayıtlı ".$customer->mtGetTesisatByTCnoResult->_KayitSayisi." adet Sözleşme Hesap numarası bulunuyor. ".$s_h_n." numaralı sözleşmeniz ile ilgili işlem yapmak veya bilgi almak için biri. Başka bir Sözleşme Hesap numarası ile işlem yapmak veya bilgi almak için dörtü. Sözleşme Hesap numaralarını tekrar dinlemek için yıldızı tuşlayınız.";

                return $this->json(["tdcID" => ["tdcID_1"=>$tdcID] , "aDurumu" => ["aDurumu_1"=>$aDurumu], 'anons' => $anons, "kayitSayisi" => $kayit_sayisi, "tdcNo" => ["tdcNo_1"=>$tdcNo]]);
            }

        }


    }
}