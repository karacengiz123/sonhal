<?php
/**
 * Created by PhpStorm.
 * User: aydn33
 * Date: 6.02.2019
 * Time: 14:50
 */

namespace App\Asterisk\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AsteriskController extends AbstractController
{

    /**
     * @Route("/assterissk", name="assterissk")
     */
    protected function assterissk()
    {

        return new Response("sad");

    }


}