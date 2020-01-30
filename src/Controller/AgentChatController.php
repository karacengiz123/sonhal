<?php

namespace App\Controller;

use App\ChatStrategy\ChatStrategyInterFace;
use App\Entity\AcwLog;
use App\Entity\AcwType;
use App\Entity\AgentBreak;
use App\Entity\BreakType;
use App\Entity\Chat;
use App\Entity\ChatMessage;
use App\Entity\Session\Sessions;
use App\Entity\User;
use App\Repository\UserRepository;
use GuzzleHttp\Client;
use PHPUnit\Util\Json;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class AgentChatController extends AbstractController
{

    public function getChat()
    {

        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        $chat = $em->getRepository(Chat::class)->findOneBy(['user' => $user, 'status' => [0, 1]]);

        return $chat;

    }

    /**
     * @IsGranted("ROLE_AGENT_CHAT")
     * @Route("/chat", name="agent_chat")
     */
    public function agentChat()
    {
        $em = $this->getDoctrine()->getManager();

        $chat = $this->getChat();

        $chatStatus = $chat instanceof Chat && $chat->getStatus() == 1 ? 2 : 1;
        /**
         * @var $user User
         */
        $user = $this->getUser();
        if ($user->getChatStatus() > 2) {
        } else {
            $user->setChatLastActivity(new \DateTime());
            $user->setChatStatus($chatStatus);
            $em->persist($user);
            $em->flush();
        }

        $nowTime = new \DateTime();
        $chatHistorys = $em->getRepository(Chat::class)->createQueryBuilder("c")
            ->where("c.user=:user")
            ->setParameter("user", $user)
            ->andWhere("c.status=:status")
            ->setParameter("status", 2)
//            ->andWhere("c.updatedAt BETWEEN :createdAtStart AND :createdAtStop")
//            ->setParameter("createdAtStart",$nowTime->format("Y-m-d 00:00:00"))
//            ->setParameter("createdAtStop",$nowTime->format("Y-m-d 23:59:59"))
            ->orderBy("c.id", "DESC")
            ->getQuery()->getresult();

        $historyChat = null;
        if (count($chatHistorys) > 0) {
            $historyChat = [];

            /**
             * @var Chat $chatHistory
             */
            foreach ($chatHistorys as $chatHistory) {
                $historyChat [] = [
                    "chat" => $chatHistory,
                    "citizen" => json_decode($chatHistory->getCitizen(), true),
                ];
            }
        }

        $breakTypesList = $this->getDoctrine()->getManager()->getRepository(BreakType::class)->findAll();
        $acwTypesList = $this->getDoctrine()->getManager()->getRepository(AcwType::class);

        $acwTypesList = $acwTypesList->createQueryBuilder("at");
        $acwTypesList
            ->where(
                $acwTypesList->expr()->notIn("at.id", [3])
            )
            ->andWhere(
                $acwTypesList->expr()->in("at.role", ["ROLE_AGENT"])
            );
        $acwTypesList = $acwTypesList->getQuery()->getResult();

        return $this->render('chat/index.html.twig', [
            'user' => $user,
            'chat' => $chat,
            'controller_name' => 'ChatController',
            'historyChat' => $historyChat,
            'breakTypesList' => $this->toJson($breakTypesList),
            'acwTypesList' => $this->toJson($acwTypesList),
        ]);
    }

    /**
     * @IsGranted("ROLE_AGENT_SEND_MESSAGE")
     * @Route("/chat/send-message", name="agent_send_message")
     */
    public function agentSendMessage(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $chat = $this->getChat();
        if ($chat instanceof Chat) {
            $chatMessage = new ChatMessage();

            $chatMessage->setMessage($request->get('message'))
                ->setChat($chat)
                ->setSender(2);

            $em->persist($chatMessage);
            $em->flush();

            return new JsonResponse(['success' => true]);
            }
        return new JsonResponse(['success' => true]);
    }

    /**
     * @Route("/chat/control", name="agent_chat_control")
     */
    public function agentChatControl()
    {
        $em = $this->getDoctrine()->getManager();

        /**
         * @var $user User
         */
        $user = $this->getUser();


        $chat = $this->getChat();

        if ($chat instanceof Chat) {

            if ($chat->getStatus() == 0) {
//                $chat->setStatus(1);
                $em->persist($chat);

//                $user->setChatStatus(2);

                $em->persist($user);
                $em->flush();
            }

            $responseArray['chatStatus'] = $chat->getStatus();
            $responseArray['citizen'] = $chat->getCitizen();
            $responseArray['chatPlanedTime'] = $chat->getStartTime()->format('m/d/Y H:i:s');
            $responseArray['chatCallId'] = "TbxPbxSystemChatCallIdNumber" . md5($chat->getId());
            $responseArray['chatId'] = $chat->getId();
        }


        $responseArray['status'] = $user->getChatStatus();


        return new JsonResponse($responseArray);
    }

    /**
     * @Route("/chat/agent-ajax", name="chat_agent_ajax")
     */
    public function chatAgentAjax()
    {
        $em = $this->getDoctrine()->getManager();

        /**
         * @var $user User
         */
        $user = $em->find(User::class, $this->getUser()->getId());
        $user->setChatLastActivity(new \DateTime());
        $em->persist($user);
        $em->flush();


        $chat = $em->getRepository(Chat::class)->findOneBy(['user' => $this->getUser(), 'status' => [1]]);
        if ($chat instanceof Chat) {
            $chatMessages = $em->getRepository(ChatMessage::class)->createQueryBuilder('cm')
                ->where('cm.chat = :chat')->setParameter('chat', $chat)
                ->getQuery()->getArrayResult();
        } else {
            return new JsonResponse(["messageCount" => 0, "messageData" => "", 'error' => "1"]);
        }

        return new JsonResponse(["messageCount" => count($chatMessages), "messageData" => $chatMessages, 'error' => "0"]);
    }

    /**
     * @Route("/api/leave-chat", name="leave_chat")
     * @param Request $request
     * @param UserInterface $user
     * @param UserRepository $userRepository
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function leaveChat(Request $request, UserInterface $user, UserRepository $userRepository)
    {
        $em = $this->getDoctrine()->getManager();

        $chat = $em->getRepository(Chat::class)->findOneBy(['user' => $this->getUser(), 'status' => [1]]);

        if ($chat instanceof Chat) {
            $chat->setStatus(2);
            $em->persist($chat);
            $em->flush();
        }
        $userRepo = $em->getRepository(User::class);
        /**
         * @var User $user
         */
        $stateChange = $userRepository->setChatState($user, 1);
        return new JsonResponse(["stateChange" => $stateChange, "status" => 1]);
    }

    /**
     * @IsGranted("ROLE_REJECT_CHAT")
     * @Route("/api/reject-chat", name="reject_chat")
     * @param Request $request
     * @param UserInterface $user
     * @param UserRepository $userRepository
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function reject(Request $request, UserInterface $user, UserRepository $userRepository)
    {
        $em = $this->getDoctrine()->getManager();
        $chat = $em->getRepository(Chat::class)->findOneBy(['user' => $this->getUser(), 'status' => [0]]);
        if ($chat instanceof Chat) {
            $chat->setStatus(0);
            $chat->setUser(null);
            $em->persist($chat);
            $em->flush();
        }
        /**
         * @var User $user
         */
        $stateChange = $userRepository->setChatState($user, 3);
        return new JsonResponse(["stateChange" => $stateChange, "status" => 3]);
    }

    /**
     * @Route("/api/to-online", name="to-online")
     * @param Request $request
     * @param UserInterface $user
     * @param UserRepository $userRepository
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function login(Request $request, UserInterface $user, UserRepository $userRepository)
    {
        /**
         * @var User $user
         */
        $stateChange = $userRepository->setChatState($user, 1);
        $closeOpenBreaks = $userRepository->closeOpenBreaks($user);
        $stateHomeChange = $userRepository->setHomeState($user, 1);
        return new JsonResponse(["stateChange" => $stateChange, "status" => 1]);
    }

    /**
     * @Route("/api/to-unavail", name="chat_tounavail")
     * @param Request $request
     * @param UserInterface $user
     * @param UserRepository $userRepository
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function toogleChatStatus(Request $request, UserInterface $user, UserRepository $userRepository)
    {
        /**
         * @var User $user
         */
        $stateChange = $userRepository->setChatState($user, 3);
        return new JsonResponse(["stateChange" => $stateChange, "status" => 3]);
    }

    /**
     * @Route("/api/accept-chat", name="login_chat")
     * @param Request $request
     * @param UserInterface $user
     * @param UserRepository $userRepository
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function acceptChat(Request $request, UserInterface $user, UserRepository $userRepository)
    {
        $em = $this->getDoctrine()->getManager();
        $chat = $em->getRepository(Chat::class)->findOneBy(['user' => $this->getUser(), 'status' => [0]]);
        $stateChange = false;
        if ($chat instanceof Chat) {
            $chat->setStatus(1);
            $em->persist($chat);
            $em->flush();

            /**
             * @var User $user
             */
            $stateChange = $userRepository->setChatState($user, 2);
        }
        return new JsonResponse(["stateChange" => $stateChange, "status" => 2]);
    }


    /**
     * @Route("/api/chatpage/state/change", name="api_chatpage_state_change")
     * @param UserInterface $user
     * @return JsonResponse
     */
    public function stateChage(UserInterface $user)
    {
        $returnData = [];
        /**
         * @var User $user
         */
        $returnData['state'] = $user->getChatStatus();
        $returnData['timeStamp'] = $user->getChatLastActivity();

        return $this->json($returnData);
    }

    public function toJson($data)
    {
        return $this->container->get('serializer')->serialize($data, 'json');
    }

    /**
     * @Route("/api/chatpage/state", name="api_chatpage_state")
     * @param UserInterface $user
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function state(UserInterface $user)
    {
        $em = $this->getDoctrine()->getManager();
        $userRepository = $em->getRepository(User::class);
        $acwLogRepository = $em->getRepository(AcwLog::class);
        $agentBreakRepository = $em->getRepository(AgentBreak::class);

        /**
         * @var User $user
         */
        $apply = $userRepository->applyOrder($user);

        $returnData = [];

        if (is_array($apply)) {

            $returnData['state'] = $apply['state'];
            $returnData['text'] = $apply['name'];
            $returnData['timeStamp'] = 0;

            $userRepository->setHomeState($user, $apply['state']);
            $userRepository->setChatState($user, 3);

        } elseif (in_array($user->getState(), [5, 11])) {

            $acwLog = $acwLogRepository->findOneBy(["user" => $user, "endTime" => null, "duration" => 0]);
            if (!is_null($acwLog)) {

                $state = $user->getState();
                $text = $acwLog->getAcwType()->getName();
                $timeStamp = $acwLog->getStartTime()->getTimestamp();

            }

        } elseif ($user->getState() == 4) {

            $agentBreak = $agentBreakRepository->findOneBy(["user" => $user, "endTime" => null, "duration" => 0]);
            if (!is_null($agentBreak)) {

                $state = $user->getState();
                $text = $agentBreak->getBreakType()->getName();
                $timeStamp = $agentBreak->getStartTime()->getTimestamp();

            }

        } elseif ($user->getState() == 1) {
            $state = 1;
            $text = "Hazır";
            $timeStamp = $user->getLastStateChange()->getTimestamp();

        } else {

            $userRepository->setHomeState($user, 1);

            $state = $user->getState();
            $text = "Hazır";
            $timeStamp = $user->getLastStateChange()->getTimestamp();

        }

        $returnData['state'] = $state;
        $returnData['chatState'] = $user->getChatStatus();
        $returnData['text'] = $text;
        $returnData['timeStamp'] = $timeStamp;


        return $this->json($returnData);
    }

    /**
     * @Route("/api/chatpage/acwLogStart/{acwType}", name="api_chatpage_acwlogstart")
     * @param UserInterface $user
     * @param UserRepository $userRepository
     * @param AcwType $acwType
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function acwLogStart(UserInterface $user, UserRepository $userRepository, AcwType $acwType)
    {
        $em = $this->getDoctrine()->getManager();

        /**
         * @var User $user
         */
        $userRepository->closeOpenBreaks($user);

        $apply = $userRepository->applyOrder($user);

        if (is_array($apply)) {
            $state = $apply['state'];
            $text = $apply['name'];
        } else {
            $acwLog = new AcwLog();
            $acwLog
                ->setUser($user)
                ->setDuration(0)
                ->setStartTime(new \DateTime())
                ->setAcwType($acwType);
            $em->persist($acwLog);
            $em->flush();

            $text = $acwType->getName();
            $state = $acwType->getState();
        }

        $userRepository->setHomeState($user, $state);
        $userRepository->setChatState($user, 3);

        return new JsonResponse(["state" => $state, "text" => $text]);
    }

    /**
     * @Route("/api/chatpage/acwLogStop", name="api_chatpage_acwlogstop")
     * @param UserInterface $user
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function acwLogStop(UserInterface $user, UserRepository $userRepository)
    {

        $em = $this->getDoctrine()->getManager();
        /**
         * @var User $user
         */
        $userRepository->closeOpenBreaks($user);

        $apply = $userRepository->applyOrder($user);
        if (is_array($apply)) {
            $state = $apply['state'];
            $text = $apply['name'];
        } else {
            $state = 1;
            $text = "Hazır";
        }
        $userRepository->setHomeState($user, $state);
        $userRepository->setChatState($user, 1);

        return new JsonResponse(["state" => $state, "text" => $text]);
    }

    /**
     * @Route("/api/chatpage/breakStart/{breakType}", name="api_chatpage_breakStart")
     * @param UserInterface $user
     * @param UserRepository $userRepository
     * @param BreakType $breakType
     * @return JsonResponse
     * @throws \Exception
     */
    public function breakStart(UserInterface $user, UserRepository $userRepository, BreakType $breakType)
    {
        $em = $this->getDoctrine()->getManager();
        /**
         * @var User $user
         */
        $userRepository->closeOpenBreaks($user);

        $apply = $userRepository->applyOrder($user);
        if (is_array($apply)) {
            $state = $apply['state'];
            $text = $apply['name'];
        } else {
            $agentBreak = new AgentBreak();
            $agentBreak
                ->setUser($user)
                ->setDuration(0)
                ->setStartTime(new \DateTime())
                ->setBreakType($breakType);

            $em->persist($agentBreak);
            $em->flush();

            $text = $breakType->getName();
            $state = 4;
        }
        $userRepository->setHomeState($user, $state);
        $userRepository->setChatState($user, 3);
        return new JsonResponse(["state" => $state, "text" => $text]);
    }

    /**
     * @Route("/api/chatpage/breakStop", name="api_chatpage_breakstop")
     * @param UserInterface $user
     * @param UserRepository $userRepository
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function breakStop(UserInterface $user, UserRepository $userRepository)
    {
        $em = $this->getDoctrine()->getManager();
        /**
         * @var User $user
         */
        $userRepository->closeOpenBreaks($user);

        $apply = $userRepository->applyOrder($user);
        if (is_array($apply)) {
            $state = 4;
            $text = $apply['name'];
        } else {
            $state = 1;
            $text = "Hazır";
        }
        $userRepository->setHomeState($user, $state);
        $userRepository->setChatState($user, 1);

        return new JsonResponse(["state" => $state, "text" => $text]);
    }

}
