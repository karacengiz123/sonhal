<?php
/**
 * Created by PhpStorm.
 * User: macbook
 * Date: 12.12.2018
 * Time: 13:30
 */

namespace App\CrmIntegration\SiebelBundle\Controller;


use App\Entity\Chat;
use App\Entity\ChatMessage;
use App\Entity\SiebelLog;
use phpDocumentor\Reflection\Types\Object_;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\User\UserInterface;
use function r\json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SiebelRESTCreateActivityController extends AbstractController
{
    /**
     * @Route("/siebelCrm/createActivity")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function curlData(Request $request)
    {

        $header = [
            'apikey' => 'jQmt1055jbbGjWeGJAQ4knAdqJ3auaMr',
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];


        $em = $this->getDoctrine()->getManager();
        $siebelLog = new SiebelLog();


        $body = $request->get('body');
        $client = new \GuzzleHttp\Client(['headers' => $header]);
        $res = $client->post($this->getParameter("siebelCreateActivityLink"), ['body' => $body]);
        $result_1 = $res->getBody()->getContents();

        $result = json_decode($result_1, true);

        if (isset($result["ActivityNumber"])) {
            $activityId = $result["ActivityNumber"];
        } else {
            $activityId = null;
        }
        /* Log a kaydet */
        // $requestSiebel = (string)$res;
        $date = new \DateTime("Europe/Istanbul");
        $bodyJson = json_decode($body);
        if (isset($bodyJson->body->CallId)) {
            $CallId = $bodyJson->body->CallId;
        } else {
            $CallId = null;
        }
        if (isset($bodyJson->body->SRId)) {
            $SRId = $bodyJson->body->SRId;
        } else {
            $SRId = null;
        }
        if (isset($bodyJson->body->ContactId)) {
            $ContactId = $bodyJson->body->ContactId;
        } else {
            $ContactId = null;
        }
        $siebelLog
            ->setCallid($CallId)
            ->setResponse($result_1)
            ->setRequest($body)
            ->setCreatedDate($date)
            ->setActivityId($activityId)
            ->setSRId($SRId)
            ->setContactId($ContactId);
        $em->persist($siebelLog);
        $em->flush();


        $this->getUser();
        return $this->json($result);
    }

    /**
     * @Route("/siebelCrm/createPollActivity")
     * @param Request $request
     * @return Response
     */
    public function createPollActivity(Request $request)
    {

//        $body = '{
//                    "body": {
//                        "UserName": "salih.inci",
//                        "Type": "Inbound",
//                        "SoruSayisi": "4",
//                        "CevapSayisi": "4",
//                        "CallId": "740CAC58-4B0811E9-A93F9A66-73656183@10.5.95.149",
//                        "Cevap1": "5",
//                        "Cevap2": "5",
//                        "Cevap3": "5",
//                        "Cevap4": "5"
//                    }
//                }';

        $body = $request->get('data');

        $header = [
            'apikey' => 'jQmt1055jbbGjWeGJAQ4knAdqJ3auaMr',
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];

        $client = new \GuzzleHttp\Client(['headers' => $header]);
        $res = $client->post($this->getParameter("createPollActivityLink"), ["body" => $body]);
        $result_1 = $res->getBody()->getContents();
        $result = json_decode($result_1, true);
        return new JsonResponse($result);
    }


    /**
     * @Route("/siebelCrm/createChatActivity/{chat}", name="siebel_crm_create_chat_activity")
     * @param Chat $chat
     * @return JsonResponse
     */
    public function createChatActivity(Chat $chat)
    {

        $chatRep = $this->getDoctrine()->getRepository(Chat::class);

        $result = $chatRep->createChatActivity($chat,$this->getParameter("createChatActivityLink"));
        return $this->json($result);
    }
}
