<?php

namespace App\Controller;


use App\Entity\LoginLog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;


class LoginLogController extends AbstractController
{
    /**
     * @Route("/logoutLog",name="logoutLog")
     */
    public function logoutLog(UserInterface $user)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(LoginLog::class)->findBy(["userId"=>$user,"EndTime"=>null]);
        if (count($repo) == 1){
            $repo[0]
                ->setEndTime(new \DateTime())
                ->setLastOnline(new \DateTime());
            $em->persist($repo[0]);
            $em->flush();
        }
        return new Response("true");
    }
}