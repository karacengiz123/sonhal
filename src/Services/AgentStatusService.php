<?php


namespace App\Services;


use App\Asterisk\Entity\Config;
use App\Entity\AcwLog;
use App\Entity\AgentBreak;
use App\Entity\User;
use App\Helpers\Date;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class AgentStatusService
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param User $user
     * @return Response
     * @throws \Exception
     */
    public function status(User $user)
    {
        $em = $this->em;

        /**
         * @todo    TEMSİLCİNİN DURUMU GEREKLİ
         */
        $internalState = [
            0 => "GİRİŞ YAPMAMIŞ",
            1 => "HAZIR",
            8 => "ÇAĞRIDA",
            2 => "ACW",
            5 => "SORU",
            6 => "DIŞ ARAMA",
            11 => "AcwLog",
            4 => "AgentBreak",
            12 => "Çalıyor",
            13 => "Bağlantı Yok",
            14 => "Bağlantı Yok",
            15 => "Bağlantı Yok",
            16 => "Yeniden Bağlantı Bekleniyor",
            17 => "Dahili Aramada",
            99 => "Anket Araması",
        ];
        $state = $user->getState();

        if($state>=0)
        {
            $nowTime = new \DateTime();

                /**
                 * @todo    TEMSİLCİNİN DURUMU START
                 */
                if (in_array($state,[2,5,6,11])){
                    $acwLog = $em->getRepository(AcwLog::class)->findOneBy(['user'=>$user, 'endTime'=> null, 'duration'=>0]);
                    if (is_null($acwLog) || is_null($acwLog->getAcwType())){
                        $status = "";
                    }else{
                        $status = $acwLog->getAcwType()->getName();
                    }
                    $time = $user->getLastStateChange()->diff($nowTime);
                    $time = $time->format("%H:%I:%S");
                }
                elseif ($state == 4){
                    $agentBreak = $em->getRepository(AgentBreak::class)->findOneBy(['user'=>$user, 'endTime'=> null, 'duration'=>0]);
                    $status = $agentBreak->getBreakType()->getName();
                    $time = $user->getLastStateChange()->diff($nowTime);
                    $time = $time->format("%H:%I:%S");
                }
                else{
                    $status = $internalState[$state];
                    $time = $user->getLastStateChange()->diff($nowTime);
                    $time = $time->format("%H:%I:%S");
                }
                $statusAndTime = $status."<br>( ".$time." )";

                return new Response($statusAndTime);

        }
        else {
            return new Response("Durum Bilgisi Yok");
        }
    }

}