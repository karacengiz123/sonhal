<?php


namespace App\Controller;


use App\Entity\LoginLog;
use App\Entity\User;
use FOS\UserBundle\Controller\SecurityController;
use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Dump\Container;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Zend\Code\Scanner\TokenArrayScanner;

class LastOnlineController extends AbstractController
{

    /**
     * @Route("/last-online-control",name="last_online_control")
     * @param UserInterface $user
     * @return Response
     * @throws \Exception
     */
    public function lastOnlineControl(UserInterface $user)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(LoginLog::class)->findOneBy(["userId"=>$user,"EndTime"=>null]);

        if (!is_null($repo)){
            $repo
                ->setLastOnline(new \DateTime());
            $em->persist($repo);
            $em->flush();
        }else{
            $loginLog = new LoginLog();
            $loginLog
                ->setStartTime(new \DateTime())
                ->setLastOnline(new \DateTime())
                ->setUserId($user);
            $em->persist($loginLog);
            $em->flush();
        }

        return new Response("OK!");
    }
}