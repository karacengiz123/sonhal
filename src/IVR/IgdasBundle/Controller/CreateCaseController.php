<?php

namespace App\IVR\IgdasBundle\Controller;

use App\Entity\IvrServiceLog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreateCaseController extends AbstractController
{

    /**
     * @Route("/ivr/igdas/pbx/crateCase/{phone}/{callId}/{tdcID}/{underDemand}/{ivrStatusCode}")
     * @param Request $request
     * @param $phone
     * @param $callId
     * @param $tdcID
     * @param $underDemand
     * @param $ivrStatusCode
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function mtCreateCase(Request $request, $phone, $callId, $tdcID, $underDemand, $ivrStatusCode)
    {
        $client = new \SoapClient($this->getParameter('igdasApiLink'), array("trace" => 1));
        $em = $this->getDoctrine()->getManager();

//        $createCase = $client->mtCreateCase($request->request->all());
        $createCase = $client->mtCreateCase([
            "pState" => "Active",
            "pTelefonNo" => $phone,
            "pTDCID" => $tdcID,
            "pTalepTipi" => $underDemand,
            "pIVRDurumKodu" => $ivrStatusCode
        ]);

        $ivrServiceLog = new IvrServiceLog();
        $ivrServiceLog
            ->setCallId($callId)
            ->setAlias("İgdaş Menü Ivr Aktivite Oluştur")
            ->setInput($phone.",".$tdcID.",".$underDemand.",".$ivrStatusCode)
            ->setRequest($client->__getLastRequest())
            ->setResponse($client->__getLastResponse())
            ->setCreatesAt(new \DateTime());
        $em->persist($ivrServiceLog);
        $em->flush();

        return new JsonResponse($createCase->mtCreateCaseResult);
    }



    /**
     * @Route("/api/ivr/igdas/pbx/createCaseForDesktop")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function mtCreateCaseForDesktop(Request $request)
    {
        $client = new \SoapClient($this->getParameter('igdasApiLink'), array("trace" => 1));
        $em = $this->getDoctrine()->getManager();

        $post = [];
        $post["body"]= $request->request->all();

        if ($post["body"]["pTDCID"] == ""){
            $createCaseDesktop = $client->mtCreateCaseForDesktop([
                "pIVRDurumKodu" => 0,
                "pTDCID" => "",
                "pAccountID" => "8736BD09-D5C7-E811-801C-0050568F49E3",
                "pTelefonNo" => $post["body"]["pTelefonNo"],
                "pBaslik" => "Yeni Talep",
                "pTalepAltKonu" => "?",
                "pSon3Menu" => "?",
                "pTalepTipi" => "Bilgi",
                "pState" => "Active",
                "pAgent" => $post["body"]["pAgent"],
                "pCallID" => $post["body"]["pCallID"],
                "pAciklama" => ""
            ]);
            $post["body"] = [
                "pIVRDurumKodu" => 0,
                "pTDCID" => "",
                "pAccountID" => "8736BD09-D5C7-E811-801C-0050568F49E3",
                "pTelefonNo" => $post["body"]["pTelefonNo"],
                "pBaslik" => "Yeni Talep",
                "pTalepAltKonu" => "?",
                "pSon3Menu" => "?",
                "pTalepTipi" => "Bilgi",
                "pState" => "Active",
                "pAgent" => $post["body"]["pAgent"],
                "pCallID" => $post["body"]["pCallID"],
                "pAciklama" => ""
            ];
        }else{
            $createCaseDesktop = $client->mtCreateCaseForDesktop($request->request->all());
        }


        $ivrServiceLog = new IvrServiceLog();
        $ivrServiceLog
            ->setCallId($request->get("pCallID"))
            ->setAlias("İgdaş Menü Agent Aktivite Oluştur")
            ->setInput(''.json_encode($post["body"]).'')
            ->setRequest($client->__getLastRequest())
        ->setResponse($client->__getLastResponse())
        ->setCreatesAt(new \DateTime());
        $em->persist($ivrServiceLog);
        $em->flush();

        return new JsonResponse($createCaseDesktop->mtCreateCaseForDesktopResult);

    }

    /**
     * @Route("/ivr/igdas/ie/link/{entityId}")
     * @param Request $request
     * @param $entityId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function igdasIELink(Request $request, $entityId)
    {
        return $this->redirect('http://igcrmiis.igdas.com.tr/IgdasCRM/main.aspx?etc=112&extraqs=%3f_gridType%3d112%26etc%3d112%26id%3d%257b'.$entityId.'%257d%26pagemode%3diframe%26preloadcache%3d1552905984187%26rskey%3d16452833&pagetype=entityrecord');
    }
}