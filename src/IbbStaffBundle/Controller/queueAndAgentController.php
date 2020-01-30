<?php


namespace App\IbbStaffBundle\Controller;


use App\Asterisk\Entity\Extens;
use App\Asterisk\Entity\QueueLog;
use App\Asterisk\Entity\Queues;
use App\Entity\QueuesMembersDynamic;
use App\Entity\SkillMember;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\UserProfile;
use App\Entity\UserSkill;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/queue-skill-managament")
 */
class queueAndAgentController extends AbstractController
{

    /**
     * @Route("/", name="queue_skill_managament")
     * @return Response
     */
    public function index()
    {
        return $this->render('@IbbStaff/pages/queueAddAgent.html.twig');
    }

    /**
     * @Route("/queue-select-two", name="queue_select_two")
     * @param Request $request
     * @return JsonResponse
     */
    public function queueAgentSelectAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $queues = $em->getRepository(Queues::class)->findAll();

        return $this->json($queues);
    }


    /**
     * @Route("/queue-as-agent-select/{queue}", name="queue_as_agent_select")
     * @param Request $request
     * @param $queue
     * @return JsonResponse
     */
    public function queueAsAgentSelect(Request $request, $queue)
    {
        $em = $this->getDoctrine()->getManager();

        $queues = $em->getRepository(UserSkill::class);
        $usp = $em->getRepository(UserProfile::class);

        $queryBuilder = $usp->createQueryBuilder('up');
        $queryBuilder
            ->where(
                $queryBuilder->expr()->in(
                    'up.extension',
                    $queues
                        ->createQueryBuilder('us')
                        ->select('us.member')
                        ->where('us.queue = :queue')
                        ->getDQL()
                )
            )
            ->setParameter('queue', $queue);
        $queryBuilder = $queryBuilder->getQuery()->getResult();

        $arr = [];
        foreach ($queryBuilder as $builder){
            $arr[]=[
                "user"=>$builder->getFirstName()." ".$builder->getLastName()
                ];
        }

        return $this->json($arr);
    }


    /**
     * @Route("/queue-select")
     * @param Request $request
     * @return JsonResponse
     */
    public function teamSelectAction(Request $request)
    {
        $queue = $this->getDoctrine()->getRepository(Queues::class)
                      ->createQueryBuilder('q')->getQuery()->getArrayResult();
        return $this->json($queue);
    }


    /**
     * @Route("/queue-skill-agent-select/{userExtension}")
     * @param Request $request
     * @param null $userExtension
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function skillAgentSelectAction(Request $request, $userExtension = null)
    {
        $agentQueues = [];

        $asteriskEm = $this->getDoctrine()->getManager();
        $agentSkills = $asteriskEm->getRepository("Main:QueuesMembersDynamic")->findBy(['member' => $userExtension]);
//        $agentSkill = $asteriskEm->getRepository(QueuesMembers::class)->findOneBy(['member' => $exten_exten]);

        $user = $asteriskEm->getRepository(UserProfile::class)->findBy(["extension"=>$userExtension]);



        foreach ($agentSkills as $agentSkill_1) {
            $queue = $agentSkill_1->getQueue();
//            $penalty = $agentSkill_1->getPenalty();

            $queues = $asteriskEm->getRepository(Queues::class);
            $description = $queues->createQueryBuilder("que")
                ->select("que.description")
                ->where("que.queue=:queue")
                ->setParameter("queue", $queue)
                ->getQuery()->getSingleScalarResult();

            $agentQueues[] = ['description' => $description];
        }
//        $userData['callerId'] = $user[0]->getUser()->getUsername();
        $userData['exten'] = $userExtension;
        return $this->json(['agentQueues' => $agentQueues, 'extens' => $userData]);
    }


}