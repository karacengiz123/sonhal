<?php
namespace App\Security\Event;

use App\Asterisk\Entity\Extens;
use App\Entity\AgentBreak;
use App\Entity\LoginLog;
use App\Entity\RealtimeQueueMembers;
use App\Entity\User;
use App\Entity\UserSkill;
use Doctrine\ORM\Event\LifecycleEventArgs;
use FOS\UserBundle\Util\UserManipulator;
use LdapTools\Bundle\LdapToolsBundle\Event\LdapLoginEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LdapLoginListener
{
    private  $container;
    private  $userManipulator;
    private  $doctrine;

    public function __construct(ContainerInterface $container, UserManipulator $userManipulator, $doctrine)
    {
        $this->container = $container;
        $this->userManipulator = $userManipulator;
        $this->doctrine = $doctrine;
    }

    public function onLdapLoginSuccess(LdapLoginEvent $event)
    {
        // Get the LDAP user that logged in...
        $user = $event->getUser();

        // Get the credentials they used for the login...
        $password = $event->getToken()->getCredentials();

        $this->userManipulator->changePassword($user->getUsername(),$password);
        $this->container->get('session')->set('lp',$password);

        $em = $this->doctrine->getManager();

        /**
         * @var UserRepository $userRepository
         */
        $userRepository = $em->getRepository(User::class);
        $userRepository->loginUser($user);

//        exit;
        // Do something with the user/password combo...
    }
}