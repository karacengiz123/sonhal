<?php
/**
 * This file is part of the LdapToolsBundle package.
 *
 * (c) Chad Sikorra <Chad.Sikorra@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Security\Guard;

use App\Entity\User;
use LdapTools\Bundle\LdapToolsBundle\Event\AuthenticationHandlerEvent;
use LdapTools\Bundle\LdapToolsBundle\Event\LdapLoginEvent;
use LdapTools\Bundle\LdapToolsBundle\Security\LdapAuthenticationTrait;
use LdapTools\Bundle\LdapToolsBundle\Security\User\LdapUserChecker;
use LdapTools\Bundle\LdapToolsBundle\Security\User\LdapUserProvider;
use LdapTools\Exception\LdapConnectionException;
use LdapTools\Operation\AuthenticationOperation;
use LdapTools\LdapManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Http\EntryPoint\BasicAuthenticationEntryPoint;

/**
 * LDAP Guard Authenticator.
 *
 * @author Chad Sikorra <Chad.Sikorra@gmail.com>
 */
class LdapGuardAuthenticator extends \LdapTools\Bundle\LdapToolsBundle\Security\LdapGuardAuthenticator
{
    use LdapAuthenticationTrait;

    /**
     * @var LdapUserChecker
     */
    protected $userChecker;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var AuthenticationSuccessHandlerInterface
     */
    protected $successHandler;

    /**
     * @var AuthenticationFailureHandlerInterface
     */
    protected $failureHandler;

    /**
     * @var AuthenticationEntryPointInterface
     */
    protected $entryPoint;

    /**
     * @var array
     */
    protected $options = [
        'hide_user_not_found_exceptions' => true,
        'username_parameter' => '_username',
        'password_parameter' => '_password',
        'domain_parameter' => '_ldap_domain',
        'post_only' => false,
        'remember_me' => false,
        'login_query_attribute' => null,
        'http_basic' => false,
        'http_basic_realm' => null,
        'http_basic_domain' => null,
    ];

    private $passwordEncoder;

    private  $doctrine;

    /**
     * LdapGuardAuthenticator constructor.
     * @param bool $hideUserNotFoundExceptions
     * @param LdapUserChecker $userChecker
     * @param LdapManager $ldap
     * @param AuthenticationEntryPointInterface $entryPoint
     * @param EventDispatcherInterface $dispatcher
     * @param AuthenticationSuccessHandlerInterface $successHandler
     * @param AuthenticationFailureHandlerInterface $failureHandler
     * @param array $options
     * @param LdapUserProvider $ldapUserProvider
     * @param UserPasswordEncoder $passwordEncoder
     * @param $doctrine
     */
    public function __construct($hideUserNotFoundExceptions = true, LdapUserChecker $userChecker, LdapManager $ldap, AuthenticationEntryPointInterface $entryPoint, EventDispatcherInterface $dispatcher, AuthenticationSuccessHandlerInterface $successHandler, AuthenticationFailureHandlerInterface $failureHandler, array $options, LdapUserProvider $ldapUserProvider, UserPasswordEncoder $passwordEncoder,$doctrine)
    {
        $this->userChecker = $userChecker;
        $this->ldap = $ldap;
        $this->entryPoint = $entryPoint;
        $this->dispatcher = $dispatcher;
        $this->successHandler = $successHandler;
        $this->failureHandler = $failureHandler;
        $this->options['hide_user_not_found_exceptions'] = $hideUserNotFoundExceptions;
        $this->ldapUserProvider = $ldapUserProvider;
        $this->options = array_merge($this->options, $options);

        $this->passwordEncoder = $passwordEncoder;
        $this->doctrine = $doctrine;
    }

    /**
     * {@inheritdoc}
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        $jump = false;
        $domain = $this->ldap->getDomainContext();

//        if ($domain == "fake.domain") {
//            $jump = true;
//        }
        try {
            $credDomain = isset($credentials['ldap_domain']) ? $credentials['ldap_domain'] : '';
            $this->switchDomainIfNeeded($credDomain);
            /** @var \LdapTools\Operation\AuthenticationResponse $response */
            if ($jump == false) {
                $response = $this->ldap->getConnection()->execute(
                    new AuthenticationOperation(
                        $this->getBindUsername($user, $this->options['login_query_attribute']),
                        $credentials['password']
                    )
                );
                if (!$response->isAuthenticated()) {

                    $this->userChecker->checkLdapErrorCode(
                        $user,
                        $response->getErrorCode(),
                        $this->ldap->getConnection()->getConfig()->getLdapType()
                    );
                    throw new CustomUserMessageAuthenticationException(
                        $response->getErrorMessage(), [], $response->getErrorCode()
                    );
                }
            }
            // No way to get the token from the Guard, need to create one to pass...
            $token = new UsernamePasswordToken($user, $credentials['password'], 'ldap-tools', $user->getRoles());
            $token->setAttribute('ldap_domain', $credDomain);
            $this->dispatcher->dispatch(
                LdapLoginEvent::SUCCESS,
                new LdapLoginEvent($user, $token)
            );
        } catch (\Exception $e) {
            if ($user->getUserCategory()->getId() != 1){
                if ($this->passwordEncoder->isPasswordValid($user,$credentials['password']) == true){
                    $em = $this->doctrine->getManager();

                    /**
                     * @var UserRepository $userRepository
                     */
                    $userRepository = $em->getRepository(User::class);
                    $userRepository->loginUser($user);
                    return true;
                }
            }else{
                $this->hideOrThrow($e, $this->options['hide_user_not_found_exceptions']);
            }
        } finally {
            $this->domain = $this->ldap->getDomainContext();
            $this->switchDomainBackIfNeeded($domain);
        }

        return true;
    }
}
