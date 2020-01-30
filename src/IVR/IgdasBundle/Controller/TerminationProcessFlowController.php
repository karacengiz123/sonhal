<?php

namespace App\IVR\IgdasBundle\Controller;

use App\Entity\IvrServiceLog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TerminationProcessFlowController extends AbstractController
{

    /**
     * @Route("/ivr/igdas/pbx/terminationProcessFlow/{callId}/{tcNo}/{tktNO}")
     * @param Request $request
     * @param $callId
     * @param $tcNo
     * @param $tktNO
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function terminationProcessFlow(Request $request, $callId, $tcNo, $tktNO)
    {

        $client = new \SoapClient($this->getParameter('igdasApiLink'), array("trace" => 1));
        $em = $this->getDoctrine()->getManager();

        $customer = $client->mtGetTesisatByTCno(['pTCno' => $tcNo]);
        $ivrServiceLog = new IvrServiceLog();
        $ivrServiceLog
            ->setCallId($callId)
            ->setAlias("İgdaş Menü Fesih İşlem Akışı")
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
            return $this->json([
                "terminationStatus" => "",
                "code" => "",
                "debt" => "",
                "tktNo" => 0,
                "record" => $kayit_sayisi
            ]);
        }else{

            $fors = $customer->mtGetTesisatByTCnoResult->_TesisatRecords->TesisatInfo;

            if (is_array($fors)){
                $tesisatNo = "";
                $i = 1;
                $tdcID = array();
                foreach ($fors as $for){
                    $tktNo[]= $for->_TuketimNoktasi;
                    $i++;
                }

                if (in_array($tktNO,$tktNo)){

                    $customer_1 = $client->mtGetTesisatByTuketimNo(['pTuketimNo' => $tktNO]);
                    $ivrServiceLog = new IvrServiceLog();
                    $ivrServiceLog
                        ->setCallId($callId)
                        ->setAlias("İgdaş Menü Fesih İşlem Akışı")
                        ->setInput($tktNO)
                        ->setRequest($client->__getLastRequest())
                        ->setResponse($client->__getLastResponse())
                        ->setCreatesAt(new \DateTime());
                    $em->persist($ivrServiceLog);
                    $em->flush();

                    $terminationStatus = $client->mtgetFesihDurumu(['pSozlesmeHesapNo' => $customer_1->mtGetTesisatByTuketimNoResult->_TesisatRecords->TesisatInfo->_SozlesmeHesapNo]);
                    $ivrServiceLog = new IvrServiceLog();
                    $ivrServiceLog
                        ->setCallId($callId)
                        ->setAlias("İgdaş Menü Fesih İşlem Akışı")
                        ->setInput($customer_1->mtGetTesisatByTuketimNoResult->_TesisatRecords->TesisatInfo->_SozlesmeHesapNo)
                        ->setRequest($client->__getLastRequest())
                        ->setResponse($client->__getLastResponse())
                        ->setCreatesAt(new \DateTime());
                    $em->persist($ivrServiceLog);
                    $em->flush();

                    $code = $client->mtGetBorcbyTDC(['pTDCID' => $customer_1->mtGetTesisatByTuketimNoResult->_TesisatRecords->TesisatInfo->_TDCID]);
                    $ivrServiceLog = new IvrServiceLog();
                    $ivrServiceLog
                        ->setCallId($callId)
                        ->setAlias("İgdaş Menü Fesih İşlem Akışı")
                        ->setInput($customer_1->mtGetTesisatByTuketimNoResult->_TesisatRecords->TesisatInfo->_TDCID)
                        ->setRequest($client->__getLastRequest())
                        ->setResponse($client->__getLastResponse())
                        ->setCreatesAt(new \DateTime());
                    $em->persist($ivrServiceLog);
                    $em->flush();

                    $debt = $client->mtGetToplamBorc(['pTDCID' => $customer_1->mtGetTesisatByTuketimNoResult->_TesisatRecords->TesisatInfo->_TDCID]);
                    $ivrServiceLog = new IvrServiceLog();
                    $ivrServiceLog
                        ->setCallId($callId)
                        ->setAlias("İgdaş Menü Fesih İşlem Akışı")
                        ->setInput($customer_1->mtGetTesisatByTuketimNoResult->_TesisatRecords->TesisatInfo->_TDCID)
                        ->setRequest($client->__getLastRequest())
                        ->setResponse($client->__getLastResponse())
                        ->setCreatesAt(new \DateTime());
                    $em->persist($ivrServiceLog);
                    $em->flush();

//                    dump($customer_1);
//                    exit();

                    return $this->json([
                        "terminationStatus" => $terminationStatus->mtgetFesihDurumuResult->_FesihBasvurusuVarMi,
                        "code" => $code->mtGetBorcbyTDCResult->_EtkinlikKodu,
                        "debt" => $debt->mtGetToplamBorcResult->_Adettoplam,
                        "tktNo" => 1,
                        "record" => $kayit_sayisi
                    ]);

                }else{
                    return $this->json([
                        "terminationStatus" => "",
                        "code" => "",
                        "debt" => "",
                        "tktNo" => 0,
                        "record" => $kayit_sayisi
                    ]);
                }


            }else{

                $tktNo = $customer->mtGetTesisatByTCnoResult->_TesisatRecords->TesisatInfo->_TuketimNoktasi;

                if ($tktNO == $tktNo){

                    $customer_1 = $client->mtGetTesisatByTuketimNo(['pTuketimNo' => $tktNO]);
                    $ivrServiceLog = new IvrServiceLog();
                    $ivrServiceLog
                        ->setCallId($callId)
                        ->setAlias("İgdaş Menü Fesih İşlem Akışı")
                        ->setInput($tktNO)
                        ->setRequest($client->__getLastRequest())
                        ->setResponse($client->__getLastResponse())
                        ->setCreatesAt(new \DateTime());
                    $em->persist($ivrServiceLog);
                    $em->flush();

                    $terminationStatus = $client->mtgetFesihDurumu(['pSozlesmeHesapNo' => $customer_1->mtGetTesisatByTuketimNoResult->_TesisatRecords->TesisatInfo->_SozlesmeHesapNo]);
                    $ivrServiceLog = new IvrServiceLog();
                    $ivrServiceLog
                        ->setCallId($callId)
                        ->setAlias("İgdaş Menü Fesih İşlem Akışı")
                        ->setInput($customer_1->mtGetTesisatByTuketimNoResult->_TesisatRecords->TesisatInfo->_SozlesmeHesapNo)
                        ->setRequest($client->__getLastRequest())
                        ->setResponse($client->__getLastResponse())
                        ->setCreatesAt(new \DateTime());
                    $em->persist($ivrServiceLog);
                    $em->flush();

                    $code = $client->mtGetBorcbyTDC(['pTDCID' => $customer_1->mtGetTesisatByTuketimNoResult->_TesisatRecords->TesisatInfo->_TDCID]);
                    $ivrServiceLog = new IvrServiceLog();
                    $ivrServiceLog
                        ->setCallId($callId)
                        ->setAlias("İgdaş Menü Fesih İşlem Akışı")
                        ->setInput($customer_1->mtGetTesisatByTuketimNoResult->_TesisatRecords->TesisatInfo->_TDCID)
                        ->setRequest($client->__getLastRequest())
                        ->setResponse($client->__getLastResponse())
                        ->setCreatesAt(new \DateTime());
                    $em->persist($ivrServiceLog);
                    $em->flush();

                    $debt= $client->mtGetToplamBorc(['pTDCID' => $customer_1->mtGetTesisatByTuketimNoResult->_TesisatRecords->TesisatInfo->_TDCID]);
                    $ivrServiceLog = new IvrServiceLog();
                    $ivrServiceLog
                        ->setCallId($callId)
                        ->setAlias("İgdaş Menü Fesih İşlem Akışı")
                        ->setInput($customer_1->mtGetTesisatByTuketimNoResult->_TesisatRecords->TesisatInfo->_TDCID)
                        ->setRequest($client->__getLastRequest())
                        ->setResponse($client->__getLastResponse())
                        ->setCreatesAt(new \DateTime());
                    $em->persist($ivrServiceLog);
                    $em->flush();

//                    dump($terminationStatus);
//                    exit();

                    return $this->json([
                        "terminationStatus" => $terminationStatus->mtgetFesihDurumuResult->_FesihBasvurusuVarMi,
                        "code" => $code->mtGetBorcbyTDCResult->_EtkinlikKodu,
                        "debt" => $debt->mtGetToplamBorcResult->_Adettoplam,
                        "tktNo" => 1,
                        "record" => $kayit_sayisi
                    ]);
                }else{
                    return $this->json([
                        "terminationStatus" => "",
                        "code" => "",
                        "debt" => "",
                        "tktNo" => 0,
                        "record" => $kayit_sayisi
                    ]);
                }

            }

        }

    }

}