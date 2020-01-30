<?php
/**
 * Created by PhpStorm.
 * User: sarpdoruk
 * Date: 27.11.2018
 * Time: 18:42
 */

namespace App\WebSocket;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Ratchet\WebSocket\WsServer;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Socket implements MessageComponentInterface
{

    protected $connections = array();

    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * A new websocket connection
     *
     * @param ConnectionInterface $conn
     */
    public function onOpen(ConnectionInterface $conn)
    {
        /**
         * @var $request Request
         */
        $request = $conn->httpRequest;
        $payload = $this->getUsernameByJwtToken($request);


        if($this->getUserByUsername($payload['username'])){
            $this->connections[] = $conn;
            echo "Giriş Başarılı";
            $conn->send(json_encode(['status' => 'succeful']));
            return;
        } else {
            echo "Giriş Başarısız";
            $conn->send('Geçersiz Kullanıcı');
            $conn->close();

        }


        echo "New connection \n";
    }

    /**
     * Handle message sending
     *
     * @param ConnectionInterface $from
     * @param string $msg
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {

            $request = $from->httpRequest;
            $payload = $this->getUsernameByJwtToken($request);

            if($user = $this->getUserByUsername($payload['username'])){
                echo "Kullancıı Var";
            }


    }

    /**
     * A connection is closed
     * @param ConnectionInterface $conn
     */
    public function onClose(ConnectionInterface $conn)
    {
        foreach($this->connections as $key => $conn_element){
            if($conn === $conn_element){
                unset($this->connections[$key]);
                break;
            }
        }
    }

    /**
     * Error handling
     *
     * @param ConnectionInterface $conn
     * @param \Exception $e
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->send("Error : " . $e->getMessage());
        $conn->close();
    }


    /**
     * Get user from email credential
     *
     * @param $username
     * @return false|User
     */
    protected function getUserByUsername($username)
    {

        $em = $this->container->get('doctrine')->getManager();
        $repo = $em->getRepository(User::class);

        $user = $repo->findOneBy(array('username' => $username));

        if($user && $user instanceof User){
            return $user;
        } else {
            return false;
        }

    }

    /**
     * @param $request
     * @param $queryString
     * @return array|bool|false
     */
    protected function getUsernameByJwtToken($request)
    {
        parse_str((string)$request->getUri()->getQuery(), $queryString);

        $bearer = $queryString['bearer'];

        $jwt_manager = $this->container->get('lexik_jwt_authentication.jwt_manager');
        $token = new JWTUserToken();
        $token->setRawToken($bearer);
        $payload = $jwt_manager->decode($token);
        return $payload;
    }
}