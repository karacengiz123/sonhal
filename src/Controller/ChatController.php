<?php

namespace App\Controller;

use App\ChatStrategy\ChatStrategyInterFace;
use App\ChatStrategy\LowChatCountFirst;
use App\Datatables\ChatDatatable;
use App\Datatables\EvaluationSummaryDatatable;
use App\Entity\AcwLog;
use App\Entity\Chat;
use App\Entity\SiebelLog;
use App\Entity\User;
use App\Repository\ChatRepository;
use App\Repository\UserRepository;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Client;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sg\DatatablesBundle\Datatable\DatatableFactory;
use Sg\DatatablesBundle\Datatable\DatatableInterface;
use Sg\DatatablesBundle\Response\DatatableResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class ChatController extends AbstractController
{
    /**
     * IsGranted("ROLE_AGENT_CHAT_A")
     * @Route("/chat_list", name="chat_list")
     * @param Request $request
     * @param DatatableFactory $datatableFactory
     * @param DatatableResponse $responseService
     * @param UserInterface $user
     * @return JsonResponse|Response
     * @throws \Exception
     */
    public function chatList(Request $request, DatatableFactory $datatableFactory, DatatableResponse $responseService,UserInterface $user)
    {


        $isAjax = $request->isXmlHttpRequest();
        /** @var DatatableInterface $datatable */
        $datatable = $datatableFactory->create(ChatDatatable::class);

        $datatable->buildDatatable();

        if ($isAjax) {
            $responseService->setDatatable($datatable);
            $datatableQueryBuilder = $responseService->getDatatableQueryBuilder();
            if (!($this->isGranted("ROLE_TAKIM_LIDERI") or $this->isGranted("ROLE_SUPERVISOR"))){

                $qb = $datatableQueryBuilder->getQb();
                $qb
                    ->where(
                        $qb->expr()->in("chat.user",$this->getUser()->getId())
                    );
            }
            return $responseService->getResponse();
        }
        return $this->render('chat/list.html.twig', array(
            'datatable' => $datatable,

        ));
    }
    /**
     * @IsGranted("ROLE_AGENT_CHAT_A")
     * @Route("/agent-chat", name="agent_chat_a")
     */
    public function agentChat()
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
//        $user->setChatLastActivity(new \DateTime());
        $user->setChatStatus(1);
        $em->persist($user);
        $em->flush();

        return $this->render('chat/index.html.twig', [
            'controller_name' => 'ChatController',
        ]);
    }

    /**
     * @Route("/agent-chat-ajax", name="agent_chat_ajax")
     */
    public function agentChatAjax()
    {
        $em = $this->getDoctrine()->getManager();

        /**
         * @var $user User
         */
        $user = $this->getUser();

        if ($user->getChatStatus() == 2) {
            echo "Chati Başlat";
        } elseif ($user->getChatStatus() == 1) {
            $chat = $em->getRepository(AcwLog::class)->findOneBy(['user' => $user, 'status' => 0]);

            if ($chat instanceof AcwLog) {
                $chat->setStatus(1);
                $user->setChatStatus(2);
                $em->persist($chat);
                $em->persist($user);
                $em->flush();

                $chatInfo = [
                    'tcID' => $chat->getTcID(),
                    'firstname' => 'Sarp Doruk',
                    'lastname' => 'ASLAN'
                ];
            }

        }
        exit;
    }

    /**
     * @Route("/chat-create-activity-cron", name="chat_create_activity_cron")
     * @return JsonResponse
     * @throws \Exception
     */
    public function chatCreateActivityCron()
    {
        $em = $this->getDoctrine()->getManager();
        $chatRepository = $em->getRepository(Chat::class);
        $userRepository = $em->getRepository(User::class);

        $activeChats = $chatRepository->findBy(["status"=>0]);

        $nowTime = new \DateTime();
        foreach ($activeChats as $activeChat){
            $diffTime = $nowTime->getTimestamp() - $activeChat->getUpdatedAt()->getTimestamp();
            if (!is_null($activeChat->getUser())){

                if ($diffTime >= 30){
                    $userRepository->setChatState($activeChat->getUser(),3);
                    $activeChat->setUpdatedAt(new \DateTime());
                    $activeChat->setUser(null);
                    $activeChat->setStatus(0);
                    $em->persist($activeChat);
                    $em->flush();
                }
            } else {
                if($diffTime >= 600) {
                    $activeChat->setUpdatedAt(new \DateTime());
                    $activeChat->setStatus(3);
                    $em->persist($activeChat);
                    $em->flush();
                }
            }
        }

        $chats = $chatRepository->createQueryBuilder('c')
            ->where('c.activityId is null')
          //  ->setParameter('inCriteria',[""," ",null])
            ->andWhere('c.status= 2')
            ->andWhere('c.user is not null')
            ->getQuery()->getResult();

        $header = [
            'apikey' => 'jQmt1055jbbGjWeGJAQ4knAdqJ3auaMr',
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];

        /**
         * @var Chat $chat
         */
        foreach ($chats as $chat){
             $chatRepository->createChatActivity($chat, $this->getParameter('createChatActivityLink'));
        }

        return new JsonResponse("OK!");
    }


    /**
     * @Route("/chat/history/detail/{chatId}", name="chat_history_detail")
     * @param $chatId
     * @return JsonResponse
     */
    public function chatHistoryDetail($chatId)
    {
        $chat = $this->getDoctrine()->getRepository(Chat::class)->find($chatId);

        if (!is_null($chat)){
            return $this->json([
                "chat"=>$chat,
                "chatMessage"=>$chat->getChatMessages(),
                "citizen"=>json_decode($chat->getCitizen(),true),
            ]);
        }
    }
}
