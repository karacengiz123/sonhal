<?php
/**
 * Created by PhpStorm.
 * User: aydn33
 * Date: 26.04.2019
 * Time: 10:30
 */

namespace App\Controller;


use App\Entity\User;
use App\Entity\UserLog;
use App\Repository\UserLogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserLogController extends AbstractController
{

    /**
     * @Route("/user-log-data/{changeUser}")
     * @param User $changeUser
     * @param UserLogRepository $userLogRepository
     */
    public function changeUser(User $changeUser, UserLogRepository $userLogRepository)
    {
        $changeUserData = $userLogRepository->findBy(["changeUser"=>$changeUser],["id"=>"DESC"]);
        $data = [];
        /**
         * @var UserLog $userLog
         */
        foreach ($changeUserData as $userLog){
            $data [] = [
                "changeUser"=>$userLog->getChangeUser(),
                "changedUser"=>$userLog->getChangedUser(),
                "oldData"=>json_decode($userLog->getOldchangeUser()),
                "newData"=>json_decode($userLog->getNewChangeUser()),
            ];
        }
        dump($data);
        exit();
    }
}