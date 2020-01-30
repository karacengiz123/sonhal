<?php

namespace App\IVR\IgdasBundle\Controller;

use App\Entity\IvrServiceLog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscriberContractAndAddressInformationController extends AbstractController
{

    /**
     * @Route("/ivr/igdas/pbx/subscriberAgreement/address/{callId}/{addressType}/{addressID}")
     * @param Request $request
     * @param $callId
     * @param $addressType
     * @param $addressID
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function address(Request $request, $callId, $addressType, $addressID)
    {
        $client = new \SoapClient($this->getParameter('igdasApiLink'), array("trace" => 1));
        $em = $this->getDoctrine()->getManager();

        $address = $client->mtGetAdres([
            'pAdresTipi' => $addressType,
            'pAdresKodu' => $addressID
        ]);
        $ivrServiceLog = new IvrServiceLog();
        $ivrServiceLog
            ->setCallId($callId)
            ->setAlias("İgdaş Menü Abone Sözleşme ve Adres Bilgileri")
            ->setInput($addressType.",".$addressID)
            ->setRequest($client->__getLastRequest())
            ->setResponse($client->__getLastResponse())
            ->setCreatesAt(new \DateTime());
        $em->persist($ivrServiceLog);
        $em->flush();

//        dump($address);
//        exit();

        if ($address->mtGetAdresResult->_Response->_ErrorMesage == ""){
            return $this->json(["result" => $address->mtGetAdresResult->_Adres, "error" => 0]);
        }else{
            return $this->json(["result" => $address->mtGetAdresResult->_Response->_ErrorMesage, "error" => 1]);
        }

//        Anadolu için 1 tuşlandığında 212 kodu olacak
//        Avrupa için 2 tuşlandığında 216 kodu olacak
//        Tuşlama 3 gelirse adres tipi Vezne olacak
//        Tuşlama 4 gelirse adres tipi HizmetB olacak
//        İlk anonsta tuşlama olduysa 1 olacak
//        * 'a basıp 2. anonsta tuşlama yapıldıysa 2 olacak


    }

    /**
     * @Route("/ivr/igdas/pbx/subscriberAgreement/unitPrice/{callId}/{tour}")
     * @param Request $request
     * @param $callId
     * @param $tour
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function unitPrice(Request $request, $callId, $tour)
    {
        $client = new \SoapClient($this->getParameter('igdasApiLink'), array("trace" => 1));
        $em = $this->getDoctrine()->getManager();

        if (in_array($tour, [1, 2, 3, 4])) {

            $address = $client->mtGetBirimFiyat(['pTuru' => $tour]);
            $ivrServiceLog = new IvrServiceLog();
            $ivrServiceLog
                ->setCallId($callId)
                ->setAlias("İgdaş Menü Abone Sözleşme ve Adres Bilgileri")
                ->setInput($tour)
                ->setRequest($client->__getLastRequest())
                ->setResponse($client->__getLastResponse())
                ->setCreatesAt(new \DateTime());
            $em->persist($ivrServiceLog);
            $em->flush();

            setlocale(LC_ALL, 'tr_TR.UTF-8');
            header('content-type:text/html;charset=utf-8');
            $date = strftime("%e %B %Y . ", strtotime(substr($address->mtGetBirimFiyatResult->_Tarih,0,10)));

            $anons = "".$date." tarihinden itibaren geçerli olmak üzere metre küp perakende Doğalgaz Satış Fiyatımız ".$address->mtGetBirimFiyatResult->_BirimFiyat." artı yüzde 18 KDV olarak belirlenmiştir. Bir üst Menüye dönmek için biri. Ana Menüye Dönmek için ikiyi. Müşteri Temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

            return $this->json([
                'error' => 0,
                "anons" => $anons
            ]);

        } else {

            $address = $client->mtGetBirimFiyat(['pTuru' => $tour]);
            $ivrServiceLog = new IvrServiceLog();
            $ivrServiceLog
                ->setCallId($callId)
                ->setAlias("İgdaş Menü Abone Sözleşme ve Adres Bilgileri")
                ->setInput($tour)
                ->setRequest($client->__getLastRequest())
                ->setResponse($client->__getLastResponse())
                ->setCreatesAt(new \DateTime());
            $em->persist($ivrServiceLog);
            $em->flush();

            return $this->json([
                'error' => $address->mtGetBirimFiyatResult->_Response->_ErrorMesage,
                "anons" => "",
            ]);

        }
    }

    /**
     * @Route("/ivr/igdas/pbx/subscriberAgreement/campaign/{callId}")
     * @param Request $request
     * @param $callId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function campaign(Request $request, $callId)
    {
        $client = new \SoapClient($this->getParameter('igdasApiLink'), array("trace" => 1));
        $em = $this->getDoctrine()->getManager();

        $campaign = $client->mtGetKampanya();
        $ivrServiceLog = new IvrServiceLog();
        $ivrServiceLog
            ->setCallId($callId)
            ->setAlias("İgdaş Menü Abone Sözleşme ve Adres Bilgileri")
            ->setInput("")
            ->setRequest($client->__getLastRequest())
            ->setResponse($client->__getLastResponse())
            ->setCreatesAt(new \DateTime());
        $em->persist($ivrServiceLog);
        $em->flush();


//        dump($campaign);
//        exit();
        if ($campaign->mtGetKampanyaResult->_Response->_ErrorMesage == ""){
            return $this->json([
                'error' => 0,
                'anons' => $campaign->mtGetKampanyaResult->_KampanyaMetni
            ]);
        }else{
            return $this->json([
                'error' => 1,
                'anons' => ""
            ]);
        }

    }

    /**
     * @Route("/ivr/igdas/pbx/subscriberAgreement/parameterValue/{callId}/{parameterName}")
     * @param Request $request
     * @param $callId
     * @param $parameterName
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function parameterValue(Request $request, $callId, $parameterName)
    {
        $client = new \SoapClient($this->getParameter('igdasApiLink'), array("trace" => 1));
        $em = $this->getDoctrine()->getManager();

        $parameter = $client->mtGetParametreDeger(['pParametreAdi' => $parameterName]);
        $ivrServiceLog = new IvrServiceLog();
        $ivrServiceLog
            ->setCallId($callId)
            ->setAlias("İgdaş Menü Abone Sözleşme ve Adres Bilgileri")
            ->setInput($parameterName)
            ->setRequest($client->__getLastRequest())
            ->setResponse($client->__getLastResponse())
            ->setCreatesAt(new \DateTime());
        $em->persist($ivrServiceLog);
        $em->flush();

//        dump($parameter);
//        exit();

        $anons = "Her bir bağımsız bölüm için belirlenen abone bağlantı bedeli ".$parameter->mtGetParametreDegerResult->_ParametreDegeri." tele dir. Bu bedel 200 metrekareden daha küçük kullanım alanı ve G4 tipi sayaç bedelini kapsamaktadır. Abonelik yapıldığı tarihte geçerli olan merkez bankası döviz alış kuruna göre TL olarak tahsil edilmektedir. Bir Üst Menüye dönmek için biri. Ana Menüye dönmek için lütfen ikiyi tuşlayınız.";

        return $this->json([
            'anons' => $anons,
        ]);

    }

}