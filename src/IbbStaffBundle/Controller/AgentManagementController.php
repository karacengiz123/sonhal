<?php
/**
 * Created by PhpStorm.
 * User: aydn33
 * Date: 19.04.2019
 * Time: 13:24
 */

namespace App\IbbStaffBundle\Controller;


use App\Entity\AcwLog;
use App\Entity\AcwType;
use App\Entity\AgentBreak;
use App\Entity\BreakType;
use App\Entity\Orders;
use App\Entity\StateLog;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/agentManagement")
 * Class AgentManagementController
 * @package App\IbbStaffBundle\Controller
 */
class AgentManagementController extends AbstractController
{

    /**
     * @Route("/status-control", name="agent_managament_status_control")
     * @param Request $request
     * @return JsonResponse
     */
    public function agentManagementBreak(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $userId = $request->get("userId");
        $status = "";
        $internal = "";
        $extension = "";
        $orderType = "";
        $orderSubType = 0;
        $orderTypeStatus = false;
        $internalState = StateLog::$internalState;

        $user = $this->getDoctrine()->getRepository(User::class)->find($userId);
        if ($user != null) {
            $state = $user->getState();
            $internal = $user->getUserProfile()->getExtension();

            $order = $em->getRepository(Orders::class)->findOneBy(["user" => $user]);
            if (!is_null($order)) {
                $orderTypeStatus = true;
                if ($order->getStartOrStop() == 1) {
                    if ($order->getType() == "AgentBreak") {
                        $status = $em->find(BreakType::class, $order->getSubType()) . " GİRMESİ BEKLENİYOR";
                    } elseif ($order->getType() == "AcwLog") {
                        $status = $em->find(AcwType::class, $order->getSubType()) . " GİRMESİ BEKLENİYOR";
                    }
                } elseif ($order->getStartOrStop() == 0) {
                    if ($order->getType() == "AgentBreak") {
                        $status = $em->find(BreakType::class, $order->getSubType()) . " ÇIKMASI BEKLENİYOR";
                    } elseif ($order->getType() == "AcwLog") {
                        $status = $em->find(AcwType::class, $order->getSubType()) . " ÇIKMASI BEKLENİYOR";
                    }
                }
            } elseif ($state == 11) {
                $acwLog = $em->getRepository(AcwLog::class)->findOneBy(['user' => $user, 'duration' => 0]);
                $status = $acwLog->getAcwType()->getName();
                $orderType = "AcwLog";
                $orderSubType = $acwLog->getAcwType()->getId();
            } elseif ($state == 4) {
                $agentBreak = $em->getRepository(AgentBreak::class)->findOneBy(['user' => $user, 'duration' => 0]);
                $status = $agentBreak->getBreakType()->getName();
                $orderType = "AgentBreak";
                $orderSubType = $agentBreak->getBreakType()->getId();
            } else {
                $orderType = "AcwLog";
                if ($state == 2) {
                    $orderSubType = 1;
                } elseif ($state == 5) {
                    $orderSubType = 2;
                } elseif ($state == 6) {
                    $orderSubType = 3;
                }
                $status = $internalState[$state];
            }
        } else {
            $status = "TEMSİLCİ BULUNAMADI";
            $internal = "";
        }

        return new JsonResponse(["status" => $status, "internal" => $internal, "orderTypeStatus" => $orderTypeStatus, "orderType" => $orderType, "orderSubType" => $orderSubType]);
    }

    /**
     * @Route("/acwLogAllStart", name="acwLogAllStart")
     * @param UserInterface $user
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function acwLogAllStart(UserInterface $user, Request $request)
    {
        $users = $request->get("users");
        $users = json_decode($users, true);
        $acwTypeId = $request->get("acwTypeId");

        $acwTypeId = json_decode($acwTypeId, true);

        $acwTypeText = $acwTypeId[0]["text"];
        $acwTypeT = $acwTypeId[0]["typeT"];
        $acwTypeId = $acwTypeId[0]["id"];

        $em = $this->getDoctrine()->getManager();
        if (count($users) > 0 && $acwTypeId != "Durum Seçiniz") {
            foreach ($users as $userId) {
                $orderDelete = $em->getRepository(Orders::class);
                $orderDelete->createQueryBuilder('o')
                    ->delete()
                    ->where('o.user = :user')
                    ->setParameter("user", $em->find(User::class, $userId["id"]))
                    ->getQuery()->execute();

                $order = new Orders();
                $order
                    ->setTeamLeader($user)
                    ->setUser($em->find(User::class, $userId["id"]))
                    ->setType($acwTypeT)
                    ->setSubType($acwTypeId)
                    ->setStartOrStop(1)
                    ->setCreatedAt(new \DateTime())
                    ->setUpdatedAt(new \DateTime());
                $em->persist($order);
                $em->flush();
            }
            return new JsonResponse(["result" => 1, "text" => "Acw Kayıtları Başlatıldı."]);
        } else {
            return new JsonResponse(["result" => 0, "text" => "Lütfen Temsilci veya ACW tipi seçiniz.."]);
        }
    }


    /**
     * @Route("/agent/logout/{user}")
     * @param User $user
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function logoutUSer(User $user)
    {
        $userRepo = $this->getDoctrine()->getRepository(User::class)->logoutUser($user);
        return $this->json(["ok"]);
    }

    /**
     * @Route("/acwLogAllStop", name="acwLogAllStop")
     * @param UserInterface $user
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function acwLogAllStop(UserInterface $user, Request $request)
    {
        $users = $request->get("users");
        $orderType = $request->get("orderType");
        $orderSubType = $request->get("orderSubType");
        $users = json_decode($users, true);
        $em = $this->getDoctrine()->getManager();
        if (count($users) > 0) {
            foreach ($users as $userId) {
                $orderDelete = $em->getRepository(Orders::class);
                $orderDelete->createQueryBuilder('o')
                    ->delete()
                    ->where('o.user = :user')
                    ->setParameter("user", $em->find(User::class, $userId["id"]))
                    ->getQuery()->execute();


                if ($orderType != "" and $orderSubType != 0) {
                    $order = new Orders();
                    $order
                        ->setTeamLeader($user)
                        ->setUser($em->find(User::class, $userId["id"]))
                        ->setType($orderType)
                        ->setSubType($orderSubType)
                        ->setStartOrStop(0)
                        ->setCreatedAt(new \DateTime())
                        ->setUpdatedAt(new \DateTime());
                    $em->persist($order);
                    $em->flush();
                }
            }
            return new JsonResponse(["result" => 1, "text" => "Acw Kayıtları Durduruldu."]);
        } else {
            return new JsonResponse(["result" => 0, "text" => "Lütfen Temsilci seçiniz.."]);
        }
    }


}