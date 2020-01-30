<?php

namespace App\IVR\IBBBundle\Controller;

use App\Entity\IvrServiceLog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IvrIettDurakSorgulamaController extends AbstractController
{

    /**
     * @Route("/ivr/iett/central/durak/karsilama/{callId}/{durakNo}")
     * @param $callId
     * @param $durakNo
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function ivrDurakKarsilama($callId, $durakNo)
    {


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getParameter('busStopApiLink').$durakNo);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json')); // Assuming you're requesting JSON
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);

        $em = $this->getDoctrine()->getManager();

        $ivrServiceLog = new IvrServiceLog();
        $ivrServiceLog
            ->setCallId($callId)
            ->setAlias("İbb Menü İett Durak Sorgulama")
            ->setInput($durakNo.", oho , oho123")
            ->setRequest($this->getParameter('busStopApiLink').$durakNo."")
            ->setResponse($response)
            ->setCreatesAt(new \DateTime());
        $em->persist($ivrServiceLog);
        $em->flush();

        $datas = json_decode($response);

        date_default_timezone_set('Europe/Istanbul');
        $tarih = date("H:i");

        $durak_adi = "";
        $text = "";

//        dump($datas);
//        exit();

        if( $datas == null) {
            $sonuc="0";
            return $this->json([
                "result" => 0
            ]);
        }
        else{
            $i=1;
            foreach ($datas as $data) {

                if ($durak_adi == "") {
                    $durak_adi = $data->durak_adi;
                }
                if ($i > 10){
                    break;
                }
                $minute = ((strtotime($data->saat) - strtotime($tarih)) / 60);
                $text .= "$data->hatkodu numaralı aracı $minute dakika sonra. ";
                $i++;

            }
            $sonuc = $durak_adi . " durağımızdan. " . $text . " ulaşacaktır.";

        }
        return $this->json([
            "result" => $sonuc
        ]);

    }


    /**
     * @Route("/ivr/iett/central/durak/iettSenMail/{callId}/{telNo}/{durakNo}")
     * @param $callId
     * @param $telNo
     * @param $durakNo
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function iettSenMail($callId, $telNo, $durakNo)
    {


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getParameter('busStopApiLink').$durakNo);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json')); // Assuming you're requesting JSON
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);

        $em = $this->getDoctrine()->getManager();

        $ivrServiceLog = new IvrServiceLog();
        $ivrServiceLog
            ->setCallId($callId)
            ->setAlias("İbb Menü İett Durak Sorgulama")
            ->setInput($durakNo.", oho , oho123")
            ->setRequest($this->getParameter('busStopApiLink').$durakNo."")
            ->setResponse($response)
            ->setCreatesAt(new \DateTime());
        $em->persist($ivrServiceLog);
        $em->flush();

        $datas = json_decode($response);

        date_default_timezone_set('Europe/Istanbul');
        $tarih = date("H:i");

        $durak_adi = "";
        $text = "";

//        dump($datas);
//        exit();

        if( $datas == null) {

            $sonuc="0";
            return $this->json([
                "result" => 0
            ]);

        }
        else{

            $i=1;
            foreach ($datas as $data) {

                if ($durak_adi == "") {
                    $durak_adi = $data->durak_adi;
                }
                if ($i > 20){
                    break;
                }
                $text .= $data->hatkodu ." => ". $data->saat."\n";
                $i++;
            }
            $sonuc = $durak_adi ."\n"."\n". $text ."\n";


            $options = array(
                'login' => "crm-bhm-sms-ws-user",
                'password' => "7S9Rg8eeeUQudBK5",
                'trace' => 1
            );
            $client = new \SoapClient($this->getParameter('busStopSMSLink'), $options);
            $customer = $client->MesajGonder([
                "MesajGonderKriter"=> [
                    "mesajDetayList"=> [
                        "mesaj"=> "$sonuc",
                        "telefonNumarasi"=> "$telNo"
                    ],
                    "smsKullaniciAdi"=> "BMASA_OTP_VODAFONE_BHM"
                ]
            ]);
            $ivrServiceLog = new IvrServiceLog();
            $ivrServiceLog
                ->setCallId($callId)
                ->setAlias("İbb Menü İett Durak Sorgu Sms Alma")
                ->setInput("crm-bhm-sms-ws-user".","."7S9Rg8eeeUQudBK5".",".$sonuc.",".$telNo.","."BMASA_OTP_VODAFONE_BHM")
                ->setRequest($client->__getLastRequest())
                ->setResponse($client->__getLastResponse())
                ->setCreatesAt(new \DateTime());
            $em->persist($ivrServiceLog);
            $em->flush();

            if (isset($customer->return->faultMessage)){
                return $this->json([
                    "result" => 0
                ]);
            }elseif (isset($customer->return->sonucBilgisi)){
                return $this->json([
                    "result" => 1
                ]);
            }

//            dump($customer);
//            exit();



        }

    }


}