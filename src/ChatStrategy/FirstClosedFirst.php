<?php


namespace App\ChatStrategy;


use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class FirstClosedFirst implements ChatStrategyInterFace
{
    /**
     * @param EntityManagerInterface $em
     * @return User
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getChatUser(EntityManagerInterface $em) {

        $user = $em->getRepository(User::class)->createQueryBuilder('u')
            ->where('u.chatStatus = 1')->orderBy('u.chatLastActivity','ASC')
            ->getQuery()->getSingleResult();

        return $user;

    }
}
