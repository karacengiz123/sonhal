<?php

namespace App\IVR\IgdasBundle\Controller;

use App\Entity\IvrServiceLog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectInformationController extends AbstractController
{

    /**
     * @Route("/ivr/igdas/pbx/projectInformation/{callId}/{tdcID}")
     * @param Request $request
     * @param $callId
     * @param $tdcID
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function projectInformation(Request $request, $callId, $tdcID)
    {
        $client = new \SoapClient($this->getParameter('igdasApiLink'), array("trace" => 1));
        $em = $this->getDoctrine()->getManager();

        $project = $client->mtGetProjebyTDC(['pTDCID' => $tdcID]);
        $ivrServiceLog = new IvrServiceLog();
        $ivrServiceLog
            ->setCallId($callId)
            ->setAlias("İgdaş Menü Proje Bilgilerİ")
            ->setInput($tdcID)
            ->setRequest($client->__getLastRequest())
            ->setResponse($client->__getLastResponse())
            ->setCreatesAt(new \DateTime());
        $em->persist($ivrServiceLog);
        $em->flush();

        $anons = "Tesisatınız için İGDAŞ a ".date("d-m-Y", strtotime($project->mtGetProjebyTDCResult->_AlinanTarih))." tarihinde verilmiş. ".date("d-m-Y", strtotime($project->mtGetProjebyTDCResult->_OnayTarihi))." tarihinde onaylanmış projenizde ".$project->mtGetProjebyTDCResult->_CihazBilgileri." kullanımı görülmektedir. Cihaz türlerinin hatalı olduğunu ve düzeltilmesi istiyorsanız biri . Bir üst menüye dönmek için lütfen: ikiyi . Müşteri temsilcisi ile görüşmek için sıfırı tuşlayınız.";

        return $this->json([
            "tdcID" => $tdcID,
            "anons" => $anons,
        ]);

    }
}