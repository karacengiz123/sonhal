<?php

namespace App\IVR\IBBBundle\Controller;

use App\Entity\IvrServiceLog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IvrCardController extends AbstractController
{


    /**
     * @Route("/ivr/card/getCardApplication/{callId}/{tck}")
     * @param $callId
     * @param $tck
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function getCardApplication($callId, $tck)
    {


//        $client = new \SoapClient("http://dev-web-uiwt.belbim.istanbul/EParaAdapter/SoapAdapter/IVRService.asmx?WSDL", array("trace" => 1));
        $client = new \SoapClient($this->getParameter('cardControllerApiLink'), array("trace" => 1));
//        $client = new \SoapClient("http://uat-app-uiwt.belbim.local/EParaAdapter/SoapAdapter/IVRService.asmx?WSDL", array("trace" => 1));

        $em = $this->getDoctrine()->getManager();

        try {
            $customer = $client->GetCardApplication([
                'AuthHeader' => [
                    'userName' => '',
                    'password' => ''
                ],
                'identityNumber' => ''.$tck.''
            ]);

            $ivrServiceLog = new IvrServiceLog();
            $ivrServiceLog
                ->setCallId($callId)
                ->setAlias("İbb Menü Kart Sorgulama")
                ->setInput($tck)
                ->setRequest($client->__getLastRequest())
                ->setResponse($client->__getLastResponse())
                ->setCreatesAt(new \DateTime());
            $em->persist($ivrServiceLog);
            $em->flush();

        } catch (\SoapFault $fault) {
            return $this->json([
                "anons" => "",
                "BirthDate" => "",
                "cardID" => "",
                "count" => "",
                "error" => 1
            ]);
        }

        $result = $customer->GetCardApplicationResult->any;

        $result = simplexml_load_string($result);

//        dump($result);
//        exit();

        setlocale(LC_ALL, 'tr_TR.UTF-8');
        header('content-type:text/html;charset=utf-8');
        $text = "";
        $i=1;
        foreach ($result->NewDataSet->Table1 as $data){
            if ($data->CardStatusName == "Açık"){
                $cardID ['cardID_'.$i] = (string)$data->MifareId;
                $birthDate ["birthDate_".$i]= str_replace('.','',(string)$data->BirthDate);
                $date = strftime("%e %B %Y . ", strtotime(substr($data->CardPressDate,0,10)));
                $card = $data->ProductName." . ";
                $text .= $date." tarihinde almış olduğunuz. ".$card." kartınızı kapatmak için ".$i." tuşuna basınız.";
                $i++;
            }
        }

        return $this->json(["anons" => $text,"BirthDate" => str_replace('.','',(string)$result->NewDataSet->Table1[0]->BirthDate),"cardID" => $cardID,"count" => $i-1, "error" => 0]);

    }

    /**
     * @Route("/ivr/card/setCardApplicationBlock/{callId}/{cardID}")
     * @param $callId
     * @param $cardID
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function setCardApplicationBlock($callId, $cardID)
    {

//        $client = new \SoapClient("http://dev-web-uiwt.belbim.istanbul/EParaAdapter/SoapAdapter/IVRService.asmx?WSDL", array("trace" => 1));
        $client = new \SoapClient($this->getParameter('cardControllerApiLink'), array("trace" => 1));
//        $client = new \SoapClient("http://uat-app-uiwt.belbim.local/EParaAdapter/SoapAdapter/IVRService.asmx?WSDL", array("trace" => 1));

        $em = $this->getDoctrine()->getManager();

        $customer = $client->SetCardApplicationBlock([
            'AuthHeader' => [
                'userName' => '',
                'password' => ''
            ],
            'cardSeriesNumber' => ''.$cardID.'',
            'doubtReasonsID' => '22',
            'description' => 'Kayıp Beyanı-Telefon'
        ]);

        $ivrServiceLog = new IvrServiceLog();
        $ivrServiceLog
            ->setCallId($callId)
            ->setAlias("İbb Menü Kart Kapama")
            ->setInput($cardID.","."22".","."Kayıp Beyanı-Telefon")
            ->setRequest($client->__getLastRequest())
            ->setResponse($client->__getLastResponse())
            ->setCreatesAt(new \DateTime());
        $em->persist($ivrServiceLog);
        $em->flush();

        $result = $customer->SetCardApplicationBlockResult;

        return $this->json([
            "result" => $result
        ]);

    }

}