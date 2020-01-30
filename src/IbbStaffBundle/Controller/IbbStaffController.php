<?php

namespace App\IbbStaffBundle\Controller;

use App\Asterisk\Entity\Agents;
use App\Asterisk\Entity\Extens;
use App\Asterisk\Entity\PsContacts;
use App\Asterisk\Entity\Queues;

use App\Entity\AcwType;
use App\Entity\BreakType;
use App\Entity\Evaluation;
use App\Entity\Parameters;
use phpDocumentor\Reflection\Types\This;

use App\Entity\RealtimeQueueMembers;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\UserProfile;
use App\Entity\UserSkill;
use LdapTools\Enums\Exchange\ELCMailbox;
use function r\asc;
use function r\ne;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\DependencyInjection\MainConfiguration;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ibb-staff")
 */
class IbbStaffController extends AbstractController
{
    /**
     * @IsGranted("ROLE_INDEX_SUPERVISOR")
     * @Route("/", name="index_supervisor")
     * @return Response
     */
    public function indexSupervisorAction()
    {
        return $this->render('@IbbStaff/pages/ibbDataTable.html.twig');
    }

    /**
     * @IsGranted("ROLE_SKILL_MANAGEMENT")
     * @Route("/skill-management", name="skill_management")
     * @return Response
     */
    public function skillManagement()
    {
        return $this->render('@IbbStaff/pages/skillManagement.html.twig');
    }

    /**
     * @Route("/agent-pause/{agent}", name="agent-pause")
     * @param Request $request
     * @param $agent
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function pauseAgentfromQues(Request $request, $agent)
    {

        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'https://'.$this->getParameter('tbxSipServer').'/api/queue.php?user=amiuser&pass=amiuser&action=pause&exten=' . $agent . '');

        return new Response("agent pause edildi");

    }

    /**
     * @Route("/agent-unpause/{agent}", name="agent-unpause")
     * @param Request $request
     * @param $agent
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function unpauseAgentfromQues(Request $request, $agent)
    {

        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'https://'.$this->getParameter('tbxSipServer').'/api/queue.php?user=amiuser&pass=amiuser&action=unpause&exten=' . $agent . '');


        return new Response("agent unpause edildi");
    }


    /**
     * @Route("/agent-select/{team}", name="agent_select")
     * @param Request $request
     * @param $team
     * @return JsonResponse
     */
        public function agentSelectAction(Request $request, $team)
    {
        $em = $this->getDoctrine()->getManager();
        $hardphone = "off";
        if ($team == "hardphone"){
            $users = $em->getRepository(Extens::class)->createQueryBuilder("e")
                ->select("e.exten")
                ->where("e.tech=:tech")
                ->setParameter("tech","SIP")
                ->getQuery()->getArrayResult();
            $hardphone = "on";
        }else{
            if ($team == 0){
                $userRepo = $em->getRepository(User::class);
                $users = $userRepo->createQueryBuilder('u')->join('u.userProfile', 'up')
                    ->select('u.id , up.firstName as first_name, up.lastName as last_name , up.extension as exten')
                    ->getQuery()->getArrayResult();
            }else{
                $userRepo = $em->getRepository(User::class);
                $users = $userRepo->createQueryBuilder('u')->join('u.userProfile', 'up')
                    ->select('u.id , up.firstName as first_name, up.lastName as last_name , up.extension as exten')
                    ->where('u.teamId=' . $team)
                    ->getQuery()->getArrayResult();
            }
        }
//        return $this->json(array_column($agents,'callerId','idx'));
        return $this->json(["hardphone"=>$hardphone,"users"=>$users]);
    }

    /**
     * @Route("/agent-select-eva/{team}", name="agent_select_eva")
     * @param Team $team
     * @return JsonResponse
     */
    public function agentSelectActionEva(Team $team)
    {
        $em = $this->getDoctrine()->getManager();

        $users = [];

        $teamUsers = $em->getRepository(User::class)->findBy(["teamId"=>$team]);

        foreach ($teamUsers as $teamUser){
            $users []= [
              "id"=>$teamUser->getId(),
              "text"=>$teamUser->__toString(),
            ];
        }

        return $this->json($users);
    }


    /**
     * @Route("/team-select", name="team_select")
     */
    public function teamSelectAction(Request $request)
    {
        $teams = $this->getDoctrine()->getRepository(Team::class)->createQueryBuilder('t')->getQuery()->getArrayResult();
        return $this->json($teams);
    }

    /**
     * @Route("/team-select-eva", name="team_select_eva")
     */
    public function teamSelectEva(Request $request)
    {
        $arr = [];
        $teams = $this->getDoctrine()->getRepository(Team::class)->findAll();
        foreach ($teams as $team){
            $arr []= [
                "id" => $team->getId(),
                "text" => $team->getName(),
            ];
        }

        return $this->json($arr);
    }

    /**
     * @Route("/acw-select", name="acw_select")
     */
    public function acwSelectAction(Request $request)
    {
        $q = $request->get('q');
        $em = $this->getDoctrine()->getManager();
        $acws = $em->getRepository(AcwType::class)->findAll();
        $breaks = $em->getRepository(BreakType::class)->findAll();
        $arr = [];
        foreach ($acws as $acw){
            if ($acw->getName() <> "DIŞ ARAMA"){
                if ($acw->getName() <> "ACW"){
                    $arr ["AcwLog"][] = $acw;
                }
            }
        }
        foreach ($breaks as $break){
            $arr ["AgentBreak"][] = $break;
        }
        return $this->json($arr);
    }

    /**
     * @Route("/skill-select", name="skill_select")
     */
    public function skillSelectAction(Request $request)
    {
        $q = $request->get('q');
        $asteriskEm = $this->getDoctrine()->getManager('asterisk');
        $agentsRepo = $asteriskEm->getRepository(Queues::class);
        $skill = $agentsRepo->createQueryBuilder('e')
            ->select('e.description as text', 'e.queue as queue')
            ->getQuery()->getArrayResult();
//        return $this->json(array_column($agents,'callerId','idx'));
        return $this->json($skill);
    }

    /**
     * @Route("/skill-agent-select/{userExtension}", name="skill_agent_select")
     * @param Request $request
     * @param null $userExtension
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function skillAgentSelectAction(Request $request, $userExtension = null)
    {
        $agentQueues = [];

        $asteriskEm = $this->getDoctrine()->getManager();
        $agentSkills = $asteriskEm->getRepository(UserSkill::class)->findBy(['member' => $userExtension]);
//        $agentSkill = $asteriskEm->getRepository(QueuesMembers::class)->findOneBy(['member' => $exten_exten]);

        $user = $asteriskEm->getRepository(UserProfile::class)->findBy(["extension"=>$userExtension]);



        foreach ($agentSkills as $agentSkill_1) {
            $queue = $agentSkill_1->getQueue();
            $penalty = $agentSkill_1->getPenalty();

            $queues = $asteriskEm->getRepository(Queues::class);
            $description = $queues->createQueryBuilder("que")
                ->select("que.description")
                ->where("que.queue=:queue")
                ->setParameter("queue", $queue)
                ->getQuery()->getSingleScalarResult();

            $agentQueues[] = ['description' => $description, 'penalty' => $penalty];
        }
        $userData['callerId'] = $user[0]->getUser()->getUsername();
        $userData['exten'] = $userExtension;
        return $this->json(['agentQueues' => $agentQueues, 'extens' => $userData]);
    }


    /**
     * @Route("/agent-information/{user}", name="agent_information")
     */
    public function agentInformationAction(Request $request, User $user = null)
    {
        $agentQueues = [];
        $userId=$user->getId();
        $userExtension = $user->getUserProfile()->getExtension();

        $asteriskEm = $this->getDoctrine()->getManager();
        //$agentInfo = $asteriskEm->getRepository("Main:QueuesMembersDynamic")->findBy(['member' => $userExtension]);
        $agentInfo = $asteriskEm->getRepository(UserProfile::class)->findOneBy(['extension' => $userExtension]);
        $agentSkills = $asteriskEm->getRepository(UserSkill::class)->findOneBy(['member' => $userExtension]);

        foreach ($agentSkills as $agentSkill) {
            $agentSkill->getQueue();
        }

    ///agent moladamı bak


        $client = new \GuzzleHttp\Client();
        $res = $client->post('https://'.$this->getParameter('appServerLink').'/agent-break-control', ["otherUser" => 1]);
        $result_1 = $res->getBody()->getContents();
        $result = json_decode($result_1, true);

        $userData['isBreak']=$result["agentBreakCount"];


    /// agent cağrıdamı bak
        $client = new \GuzzleHttp\Client();
        $callStatusResponse = $client->request('GET', 'https://'.$this->getParameter('tbxSipServer').'/api/exten.php?user=amiuser&pass=amiuser&exten=' . $userExtension . '');
        $callStatusResponse=$callStatusResponse->getBody()->getContents();



        $userData['firstName'] = $agentInfo->getFirstName();
        $userData['lastName'] = $agentInfo->getLastName();
        $userData['callerId'] = $user->getUserProfile()->__toString();
        $userData['exten'] = $user->getUserProfile()->getExtension();

        return $this->json(['agentQueues' => $agentQueues, 'extens' => $userData, 'agentCallStatus' => $callStatusResponse]);
    }

    /**
     * @var $request
     */
    private $request;

    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack;
    }

    /**
     * @Route("/agent-skill-update", name="agent_skill_update")
     * @return Response
     * @Method({"POST"})
     */
    public function agentSkillUpdate(Request $request)
    {
        $agent_exten = $request->get('agent_exten');
        $skill_queue = $request->get('skill_id');

        $jsons_agent_extens = json_decode($agent_exten, true);
        $jsons_skill_queues = json_decode($skill_queue, true);

        foreach ($jsons_agent_extens as $jsons_agent_exten) {

            foreach ($jsons_skill_queues as $jsons_skill_queue) {

                $agent = $jsons_agent_exten["id"];

                $skill = $jsons_skill_queue["id"];

                $level = $request->get('level');


                $asteriskEm = $this->getDoctrine()->getManager();
                $agentSkill = $asteriskEm->getRepository(UserSkill::class)->findOneBy(['member' => $agent, 'queue' => $skill]);


                if (!is_null($agentSkill)){
                    $agentSkill
                        ->setPenalty($level);

                    $asteriskEm->persist($agentSkill);
                    $asteriskEm->flush();

                }

                $rtqm = $asteriskEm->getRepository(RealtimeQueueMembers::class)->findOneBy(["membername"=>$agent,"queueName"=>$skill]);

                if (!is_null($rtqm)){
                    $rtqm
                        ->setPenalty($level);
                    $asteriskEm->persist($rtqm);
                    $asteriskEm->flush();
                }

                $asteriskEm = $this->getDoctrine()->getManager();
                $agentSkills = $asteriskEm->getRepository(UserSkill::class)->findBy(['member' => $agent]);
//        $agentSkill = $asteriskEm->getRepository(QueuesMembersDynamic::class)->findOneBy(['member' => $exten_exten]);

            }
        }
        foreach ($agentSkills as $agentSkill_1) {
            $queue = $agentSkill_1->getQueue();
            $penalty = $agentSkill_1->getPenalty();

            $description = $asteriskEm->getRepository(Queues::class)->findOneBy(['queue' => $queue])->getDescription();

            $agentQueues[] = ['description' => $description, 'penalty' => $penalty];
        }

        return $this->json(['agentQueues' => $agentQueues, '$skill_queue' => $skill_queue, 'update' => 'Skill Başarıyla Güncellendi']);

    }


    /**
     * @Route("/agent-skill-add-activation", name="agent_skill_add_activation")
     * @param Request $request
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function agentSkillAddActivation(Request $request)
    {


        if ($request->get('hardphone') == 1){
            $sipReg = "SIP/";
        }else{
            $sipReg = "PJSIP/";
        }

        $agent_exten = $request->get('agent_exten');
        $skill_queue = $request->get('skill_id');

        $jsons_agent_extens_add = json_decode($agent_exten, true);
        $jsons_skill_queues_add = json_decode($skill_queue, true);

//        return new JsonResponse($jsons_agent_extens_add);

        foreach ($jsons_agent_extens_add as $row_agent_add) {

            foreach ($jsons_skill_queues_add as $row_queues_add) {

                $agent = $row_agent_add["id"];

                $skill = $row_queues_add["id"];

                $level = $request->get('level');


                $asteriskEm = $this->getDoctrine()->getManager();
                $agentSkill = $asteriskEm->getRepository(UserSkill::class)->findOneBy(['member' => $agent, 'queue' => $skill]);
                $register = $asteriskEm->getRepository(RealtimeQueueMembers::class)->findBy(["membername"=>$agent]);

                if ($agentSkill instanceof UserSkill == false) {
                    $agentSkill = new UserSkill();
                    $agentSkill
                        ->setMember($agent)
                        ->setQueue($skill)
                        ->setPenalty($level);

                    $asteriskEm->persist($agentSkill);
                    $asteriskEm->flush();

                } else {
                    $agentSkill
                        ->setPenalty($level);

                    $asteriskEm->persist($agentSkill);
                    $asteriskEm->flush();

                }

                if (count($register) > 0){
                    $rtqm = $asteriskEm->getRepository(RealtimeQueueMembers::class)->findOneBy(["membername"=>$agent,"queueName"=>$skill]);

                    if (is_null($rtqm)){
                        $rtqm = new RealtimeQueueMembers();
                        $rtqm
                            ->setQueueName($skill)
                            ->setInterface("Local/" . $agent . "@from-internal-hosted/n")
                            ->setMembername($agent)
                            ->setStateInterface($sipReg . $agent)
                            ->setPenalty($level)
                            ->setPaused($register[0]->getPaused())
                            ->setUser($register[0]->getUser());
                        $asteriskEm->persist($rtqm);
                        $asteriskEm->flush();
                    }else{
                        $rtqm
                            ->setPenalty($level);
                        $asteriskEm->persist($rtqm);
                        $asteriskEm->flush();
                    }
                }

            }
        }
        return new Response("Skill Başarıyla Eklendi");
    }

    /**
     * @Route("/agent-skill-delete", name="agent_skill_delete")
     * @param Request $request
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function agentSkillDelete(Request $request)
    {

        $agent_exten = $request->get('agent_exten');
        $skill_queue = $request->get('skill_id');

        $jsons_agent_extens = json_decode($agent_exten, true);
        $jsons_skill_queues = json_decode($skill_queue, true);

        foreach ($jsons_agent_extens as $jsons_agent_exten) {

            foreach ($jsons_skill_queues as $jsons_skill_queue) {

                $agent = $jsons_agent_exten["id"];

                $skill = $jsons_skill_queue["id"];


                $asteriskEm = $this->getDoctrine()->getManager();
                $agentSkill = $asteriskEm->getRepository(UserSkill::class)->findOneBy(['member' => $agent, 'queue' => $skill]);

                if (!is_null($agentSkill)){
                    $asteriskEm->remove($agentSkill);
                    $asteriskEm->flush();
                }

                $rtqm = $asteriskEm->getRepository(RealtimeQueueMembers::class)->findOneBy(["membername"=>$agent,"queueName"=>$skill]);

                if (!is_null($rtqm)){
                    $asteriskEm->remove($rtqm);
                    $asteriskEm->flush();
                }

            }
        }

        return new Response('Skill Başarıyla Silindi');
    }


    /**
     * @Route("/agent-management", name="agent_management")
     * @return Response
     */
    public function agentManagement()
    {
        $listenerExten=$this->getParameter("listenerExten");

        return $this->render('@IbbStaff/pages/agentManagement.html.twig',[ 'listenerExten' => $listenerExten]);

    }

    /**
     * @Route("/test-select/{extens}", name="test_select")
     */
    public function testSelectAction(Request $request, Extens $extens = null)
    {
        $extens->getIdx();
        return $this->json($extens);
    }

    /**
     * @Route("/cc-pulse", name="cc_pulse")
     * @return Response
     */
    public function ccPulseAction()
    {
        return $this->render('@IbbStaff/pages/ccPulse.html.twig');
    }

    /**
     * @Route("/cc-pulse/outbound", name="outbound_cc_pulse")
     * @return Response
     */
    public function ccPulseOutboundAction()
    {
        return $this->render('@IbbStaff/pages/ccPulseOutbound.html.twig');
    }

    /**
     * @Route("/cc-pulse/chat", name="chat_cc_pulse")
     * @return Response
     */
    public function ccPulseChatAction()
    {
        return $this->render('@IbbStaff/pages/ccPulseChat.html.twig');
    }

    /**
     * @Route("/break-follow", name="break_follow")
     * @return Response
     */
    public function breakFollow()
    {

        $asteriskEm = $this->getDoctrine()->getManager('asterisk');
        $agentsRepo = $asteriskEm->getRepository(Extens::class);

        $agents = $agentsRepo->findAll();

        return $this->render('@IbbStaff/pages/breakFollow.html.twig', array(
            'agents' => $agents,
        ));
    }

}