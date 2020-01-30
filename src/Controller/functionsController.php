<?php


namespace App\Controller;


use ApiPlatform\Core\Hal\Serializer\ObjectNormalizer;
use ApiPlatform\Core\Serializer\JsonEncoder;
use App\Entity\User;
use App\Repository\UserLogRepository;
use App\Repository\UserRepository;
use phpDocumentor\Reflection\DocBlock\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use \Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use function r\json;

/**
 * @IsGranted("ROLE_BHM_TEKNOLOJI")
 * @Route("bhm")
 * Class functionsController
 * @package App\Controller
 */
class functionsController extends AbstractController
{
    public function toJson($data)
    {
        return $this->container->get('serializer')->serialize($data, 'json');
    }

    public function toJsonDecode($data)
    {
        return $this->container->get('serializer')->deserialize(["asd"=>"asd"],"array","json");
    }

    /**
     * @Route("/get-user-roles/{username}", name="bhm_get_user_roles")
     * @param $username
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function getUsersRoles($username, UserRepository $userRepository)
    {
        $user = $userRepository->findOneBy(["username"=>$username]);
        return $this->json($user->getRoles());
    }

    /**
     * @Route("/get-user-logs/{username}", name="bhm_get_user_logs")
     * @param $username
     * @param UserRepository $userRepository
     * @param UserLogRepository $userLogRepository
     * @return JsonResponse
     */
    public function getUserChangeData($username, UserRepository $userRepository, UserLogRepository $userLogRepository)
    {
        $user = $userRepository->findOneBy(["username"=>$username]);
        $userLogs = $userLogRepository->findBy(["changeUser"=>$user]);
        $userLogsRes = [];
        foreach ($userLogs as $userLog){
//            $oldChangeUserDecode = json_decode(,true);

//            $oldChangeUser = $this->toJsonDecode($userLog->getOldchangeUser());

//            dump($oldChangeUserDecode);
//            dump($oldChangeUser);
            exit();

//            /**
//             * @var User $newChangeUser
//             */
//            $newChangeUser = $this->json(json_decode($userLog->getOldchangeUser(),true));
            $userLogsRes []= [
                "changedUser"=>[
                    "user"=>$userLog->getChangedUser(),
                    "userProfile"=>$userLog->getChangedUser()->getUserProfile(),
                    "roles"=>$userLog->getChangedUser()->getRoles(),
                    "groups"=>$userLog->getChangedUser()->getGroups()->toArray(),
                    "breakGroup"=>$userLog->getChangedUser()->getBreakGroup(),
                    "team"=>$userLog->getChangedUser()->getTeamId(),
                ],
                "changeUser"=>[
                    "user"=>$userLog->getChangeUser(),
                    "userProfile"=>$userLog->getChangeUser()->getUserProfile(),
                    "roles"=>$userLog->getChangeUser()->getRoles(),
                    "groups"=>$userLog->getChangeUser()->getGroups()->toArray(),
                    "breakGroup"=>$userLog->getChangeUser()->getBreakGroup(),
                    "team"=>$userLog->getChangeUser()->getTeamId(),
                ],
                "oldUserData"=>[
                    "user"=>$oldChangeUser,
                    "userProfile"=>$oldChangeUser->getUserProfile(),
                    "roles"=>$oldChangeUser->getRoles(),
                    "groups"=>$oldChangeUser->getGroups()->toArray(),
                    "breakGroup"=>$oldChangeUser->getBreakGroup(),
                    "team"=>$oldChangeUser->getTeamId(),
                ],
                "newUserData"=>[
                    "user"=>$newChangeUser,
                    "userProfile"=>$newChangeUser->getUserProfile(),
                    "roles"=>$newChangeUser->getRoles(),
                    "groups"=>$newChangeUser->getGroups()->toArray(),
                    "breakGroup"=>$newChangeUser->getBreakGroup(),
                    "team"=>$newChangeUser->getTeamId(),
                ],
            ];
        }
        return $this->json($userLogsRes);
    }
}