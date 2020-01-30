<?php

namespace App\Controller;


use App\WebSocket\Socket;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SayHelloController extends AbstractController
{
    /**
     * @Route("/say/hello", name="say_hello")
     */
    public function index(Request $request)
    {
        $server =  IoServer::factory(new HttpServer(
            new WsServer(
                new Socket($this->container())
            )
        ), 8080);

        $server->run();
        return new Response('Merhaba Salih İnci');
    }
}
