<?php


namespace App\ChatStrategy;


use App\Entity\User;

class LowChatCountFirst implements ChatStrategyInterFace
{
    public function getChatUser($em)
    {
        $AcwLogQuery = $em->getRepository(User::class)->createQueryBuilder('u')
            ->join('u.AcwLogs', 'c')
            ->select('count(c.id) as count , u.id')
            ->where('u.AcwLogStatus = 1')
            ->where('c.startTime > :startTime')
            ->setParameter('startTime', new \DateTime(date('Y-m-d H:i:s', strtotime("midnight"))))
            ->groupBy('c.user')
            ->addGroupBy('u.id')
            ->setMaxResults(3)
            ->orderBy('count', 'ASC')
            ->addOrderBy('u.AcwLogLastActivity', 'DESC')
            ->setMaxResults(1);;

        $AcwLogUserID = $AcwLogQuery->getQuery()->getSingleResult(1);
        $AcwLogUser = $em->find(User::class, $AcwLogUserID['id']);
        return $AcwLogUser;
    }
    /*
    **
    * @param \Doctrine\Common\Persistence\ObjectManager $em
    * @return User|object|null
    * @throws \Doctrine\ORM\NoResultException
    * @throws \Doctrine\ORM\NonUniqueResultException
    */

}