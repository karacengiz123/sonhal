<?php
/**
 * Created by PhpStorm.
 * User: macbook
 * Date: 28.11.2018
 * Time: 19:54
 */

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;

class JWTCreatedListener
{


    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack, TokenStorageInterface $tokenStorage)
    {
        $this->requestStack = $requestStack;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param JWTCreatedEvent $event
     *
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $payload = $event->getData();
        if ($this->tokenStorage->getToken()->getUser()->getUserProfile()) {
            $payload['extension'] = $this->tokenStorage->getToken()->getUser()->getUserProfile()->getExtension();
        }
        $event->setData($payload);

        $header = $event->getHeader();
        $header['cty'] = 'JWT';

        $event->setHeader($header);
    }
}