<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2019-02-21
 * Time: 01:18
 */

namespace App\EventListener;


use App\Entity\QueuesMembersDynamic;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class QueueMembersDynamicListener
{

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var $request
     */
    private $request;

    public function __construct(TokenStorage $tokenStorage, RequestStack $requestStack)
    {
        $this->tokenStorage = $tokenStorage;
        $this->request = $requestStack;
    }


    /**
     * @param QueuesMembersDynamic $queuesMembersDynamic
     * @param LifecycleEventArgs $args
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function prePersist(QueuesMembersDynamic $queuesMembersDynamic, LifecycleEventArgs $args)
        {
            $queue = $this->request->getCurrentRequest()->getContent();

            $queueDecode = json_decode($queue, true);

            $agent_exten = $queueDecode["queue"];
            $skill_queue = $queueDecode["member"];
            $level = $queueDecode["penalty"];

            $jsons_agent_extens = json_decode($agent_exten, true);
            $jsons_skill_queues= json_decode($skill_queue, true);


            foreach ($jsons_agent_extens as $jsons_agent_exten){

                foreach ($jsons_skill_queues as $jsons_skill_queue){

                    /**
                     * İsimlendirme konusunda neden bu kadar cimrisiniz?
                     * exten alıp neden agente tanımlıyorsunuz ???
                     */
                    $exten = $jsons_agent_exten["exten"];

                    $skill = $jsons_skill_queue["queue"];

                    $asteriskEm = $args->getEntityManager();
                    $queuesMembersDynamic->setQueue("$skill");
                    $queuesMembersDynamic->setMember("$exten");
                    $queuesMembersDynamic->setPenalty("$level");
                    try {
                        $agentSkill = $asteriskEm->getRepository(QueuesMembersDynamic::class)
                            ->createQueryBuilder("qd")
                            ->select("count(qd.id)")
                            ->where("qd.queue=:queue")
                            ->setParameter("queue",$skill)
                            ->andWhere("qd.member=:member")
                            ->setParameter("member",$exten)
                            ->getQuery()->getSingleScalarResult();

                    } catch (Throwable $t) {
                        // you may want to add some logging here...
                        continue;
                    }



                    if($agentSkill === 0){
                        $queuesMembersDynamic->setQueue("$skill");
                        $queuesMembersDynamic->setMember("$exten");
                        $queuesMembersDynamic->setPenalty("$level");
                    }else{
                        throw new \Exception("Skill Tanımlı");
                        continue;
                    }
                }
            }
            return new Response("Başarılı");

        }


    public function preRemove (LifecycleEventArgs $args)
    {

        //        Kayıt İşlemi
        $entity = $args->getEntity();


        if ($entity instanceof QueuesMembersDynamic) {

            $em = $args->getEntityManager();


            $extension = $entity->getMember();
            $queue = $entity->getQueue();
            $penalty=$entity->getPenalty();
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', 'https://'.$this->getParameter('tbxSipServer').'/api/queue.php?user=amiuser&pass=amiuser&action=add&queue='.$queue.'&penalty='.$penalty.'&exten='.$extension.'');

            if ($response) {
                $em->persist();

            }
        }
        //        Kayıt İşlemi



        $entity = $args->getEntity();


        if ($entity instanceof QueuesMembersDynamic) {

            $em = $args->getEntityManager();


            $extension = $entity->getMember();
            $queue = $entity->getQueue();
            $penalty=$entity->getPenalty();
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', 'https://'.$this->getParameter('tbxSipServer').'/api/queue.php?user=amiuser&pass=amiuser&action=remove&queue='.$queue.'&penalty='.$penalty.'&exten=' . $extension . '');

            if ($response) {
                $em->persist();

            }
        }

    }



}