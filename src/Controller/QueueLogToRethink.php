<?php
/**
 * Created by PhpStorm.
 * User: aydn33
 * Date: 6.02.2019
 * Time: 16:43
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Annotation\Route;
use r;
class QueueLogToRethink extends AbstractController
{


    /**
     * @Route("/q2r")
     * @return Response
     */
    public function action()
    {

        $feed = r\table('queue_log')->changes()->run();
        foreach ($feed as $change) {
            print_r($change);
        }
    }


}