<?php
/**
 * Created by PhpStorm.
 * User: aydn33
 * Date: 26.04.2019
 * Time: 10:30
 */

namespace App\Controller;


use App\Entity\AcwType;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\AcwLog;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * @Route("/api")
 * Class AcwController
 * @package App\Controller
 */
class AcwController extends AbstractController
{
    /**
     * @Route("/softphone/acwLogStart/{acwType}/{callId}", name="softPhoneAcwLogStart")
     * @param UserInterface $user
     * @param UserRepository $userRepository
     * @param AcwType $acwType
     * @param $callId
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function acwLogStart(UserInterface $user, UserRepository $userRepository, AcwType $acwType, $callId)
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

            if ($acwType->getId() == 1){
                $acwLog->setCallId($callId);
            }

            $em->persist($acwLog);
            $em->flush();

            $text = $acwType->getName();
            $state = $acwType->getState();
        }

        $userRepository->setState($user, $state);

        return new JsonResponse(["state" => $state, "text" => $text]);
    }

    /**
     * @Route("/softphone/acwLogStop", name="softPhoneAcwLogStop")
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
        $userRepository->setState($user, $state);

        return new JsonResponse(["state" => $state, "text" => $text]);
    }
}