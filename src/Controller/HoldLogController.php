<?php
/**
 * Created by PhpStorm.
 * User: aydn33
 * Date: 26.04.2019
 * Time: 10:30
 */

namespace App\Controller;


use App\Entity\HoldLog;
use App\Helpers\Date;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/api")
 * Class HoldLogController
 * @package App\Controller
 */
class HoldLogController extends AbstractController
{
    /**
     * @Route("/hold-log-start/{callID}/{callType}", name="hold_log_start")
     * @param $callID
     * @param $callType
     * @param UserInterface $user
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function holdLogStart(UserInterface $user, $callID, $callType)
    {
        $em=$this->getDoctrine()->getManager();

        $holdLog = new HoldLog();
        $holdLog
            ->setUser($user)
            ->setUniqueId($callID)
            ->setCallType($callType)
            ->setStartTime(new \DateTime("Europe/Istanbul"))
            ->setDuration(0);

        $em->persist($holdLog);
        $em->flush();

        return $this->json(["return"=>"Kayıt Başarılı"]);
    }

    /**
     * @Route("/hold-log-stop/{callID}/{callType}", name="hold_log_stop")
     * @param $callID
     * @param $callType
     * @param UserInterface $user
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function holdLogStop(UserInterface $user, $callID, $callType)
    {
        $em=$this->getDoctrine()->getManager();

        $holdLogs = $em->getRepository(HoldLog::class)->findBy(["user"=>$user,"uniqueId"=>$callID,"callType"=>$callType,"endTime"=>null]);
        foreach ($holdLogs as $holdLog){
            $duration = Date::diffDateTimeToSecond(new \DateTime("Europe/Istanbul"),$holdLog->getStartTime());
            $holdLog
                ->setEndTime(new \DateTime("Europe/Istanbul"))
                ->setDuration($duration);
            $em->persist($holdLog);
            $em->flush();
        }
        return $this->json(["return"=>"Kayıt Başarılı"]);
    }
}