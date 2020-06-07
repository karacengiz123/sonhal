<?php
namespace App\Security\Guard;

use Symfony\Component\HttpFoundation\Request;
use LdapTools\Bundle\LdapToolsBundle\Security\LdapGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class LdapWithJWTGuard extends LdapGuardAuthenticator
{

    protected function getRequestParameter($param, Request $request)
    {
        $value = null;

        $jsonData = json_decode($request->getContent(),true);

        if(is_array($jsonData) and key_exists($param,$jsonData))
            $value = $jsonData[$param];

        return $value;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
         parent::onAuthenticationSuccess($request, $token, $providerKey); // TODO: Change the autogenerated stub
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
         parent::onAuthenticationFailure($request, $exception); // TODO: Change the autogenerated stub
    }


}