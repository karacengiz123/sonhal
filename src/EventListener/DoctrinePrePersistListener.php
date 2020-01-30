<?php
/**
 * Created by PhpStorm.
 * User: sarpdoruk
 * Date: 27.11.2018
 * Time: 12:58
 */

namespace App\EventListener;


use App\Entity\AgentBreak;
use App\Entity\User;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class DoctrinePrePersistListener
{
    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    public function __construct(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        $em = $args->getEntityManager();

        if ($em->getClassMetadata(get_class($args->getEntity()))->hasAssociation('user') and $this->tokenStorage->getToken()->getUser() instanceof User) {
            $entity->setUser($this->tokenStorage->getToken()->getUser());
        }

    }

}