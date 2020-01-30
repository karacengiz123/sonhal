<?php


namespace App\Controller;


use App\Asterisk\Entity\Extens;
use App\Entity\AcwLog;
use App\Entity\AgentBreak;
use App\Entity\HoldLog;

use App\Entity\RealtimeQueueMembers;
use App\Entity\RegisterLog;
use App\Entity\User;
use App\Entity\UserProfile;
use App\Entity\UserSkill;
use App\Helpers\Date;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class LastRegisterController extends AbstractController
{

}