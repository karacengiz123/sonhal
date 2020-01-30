<?php

// LogoutListener.php - Change the namespace according to the location of this class in your bundle
namespace App\EventListener;

use App\Asterisk\Entity\Extens;
use App\Entity\AcwLog;
use App\Entity\AgentBreak;
use App\Entity\LoginLog;
use App\Entity\RealtimeQueueMembers;
use App\Entity\RegisterLog;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;
use FOS\UserBundle\Model\UserManagerInterface;

class SessionLogoutHandler implements LogoutHandlerInterface
{

    protected $userManager;
    protected $doctrine;

    public function __construct(UserManagerInterface $userManager, $doctrine)
    {
        $this->userManager = $userManager;
        $this->doctrine = $doctrine;
    }

    public function logout(Request $request, Response $response, TokenInterface $token)
    {
        $em = $this->doctrine->getManager();

        /**
         * @var User $user
         */
        $user = $token->getUser();
        if ($user == "anon."){
            return new RedirectResponse("/login");
        }

        /**
         * @var UserRepository $userRepo
         */
        $userRepo = $em->getRepository(User::class);

        $userRepo->logoutUser($user);

        $request->getSession()->invalidate();
    }
}