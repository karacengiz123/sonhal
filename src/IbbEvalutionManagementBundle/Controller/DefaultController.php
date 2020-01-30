<?php

namespace App\IbbEvalutionManagementBundle\Controller;


use App\Asterisk\Entity\Cdr;
use App\Asterisk\Entity\QueueLog;
use App\Datatables\EvaluationListDatatable;
use App\Datatables\EvaluationSummaryDatatable;
use App\Datatables\OutboundCallDatatable;
use App\Datatables\InboundCallDatatable;
use App\Entity\Calls;
use App\Entity\Evaluation;
use App\Entity\EvaluationAnswer;
use App\Entity\EvaluationExtraSource;
use App\Entity\EvaluationReasonType;
use App\Entity\EvaluationResetReason;
use App\Entity\EvaluationSource;
use App\Entity\EvaluationStatus;
use App\Entity\FormQuestion;
use App\Entity\FormQuestionOption;
use App\Entity\FormTemplate;
use App\Entity\Group;
use App\Entity\HoldLog;
use App\Entity\LogEvaluation;
use App\Entity\RecordListenLog;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\UserProfile;
use App\Entity\UserSkill;
use App\Form\EvaluationFormType;
use App\Form\EvaluationType;
use App\Repository\CallsRepository;
use App\Repository\EvaluationExtraSourceRepository;
use Grpc\Call;
use mysql_xdevapi\Result;
use r\Queries\Dates\Date;
use r\ValuedQuery\Json;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sg\DatatablesBundle\Datatable\DatatableFactory;
use Sg\DatatablesBundle\Datatable\DatatableInterface;
use Sg\DatatablesBundle\Response\DatatableResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Templating\EngineInterface;
use Twig\Source;
use Doctrine\Common\Persistence\ObjectManager;
use function Doctrine\ORM\QueryBuilder;
use function GuzzleHttp\Promise\all;


class DefaultController extends AbstractController
{

    /**
     * @IsGranted("ROLE_QUALITY_SUMMARY")
     * @Route("/summary", name="quality_summary")
     * @param Request $request
     * @param DatatableFactory $datatableFactory
     * @param DatatableResponse $responseService
     * @param UserInterface $user
     * @return JsonResponse|Response
     * @throws \Exception
     */
    public function summary(Request $request, DatatableFactory $datatableFactory, DatatableResponse $responseService,UserInterface $user)
    {

        $periodStartMonth = ((ceil(date('m') / 3) - 1) * 3) + 1;
        $period = new \DateTime(Date('Y') . "-" . $periodStartMonth . "-01");

        $isAjax = $request->isXmlHttpRequest();
        /** @var DatatableInterface $datatable */
        $datatable = $datatableFactory->create(EvaluationSummaryDatatable::class);

        $datatable->buildDatatable();

        if ($isAjax) {
            $responseService->setDatatable($datatable);
            $datatableQueryBuilder = $responseService->getDatatableQueryBuilder();
            if ($this->isGranted("ROLE_TAKIM_LIDERI") or $this->isGranted("ROLE_SUPERVISOR")){
                /**
                 * @var User $user
                 */
                $teamsId = $this->teamsId($user);

                $qb = $datatableQueryBuilder->getQb();
                $qb
                    ->where(
                        $qb->expr()->in("user.teamId",$teamsId)
                    );
            }
            return $responseService->getResponse();
        }
        return $this->render('@IbbEvalutionManagement/quality/index.html.twig', array(
            'datatable' => $datatable,

        ));
    }

    /**
     * @param User $user
     * @return array
     */
    public function teamsId(User $user)
    {
        /**
         * @var User $user
         */
        $teams = $this->getDoctrine()->getRepository(Team::class)->createQueryBuilder("t")
            ->where("t.manager=:manager")
            ->setParameter("manager", $user)
            ->orWhere("t.managerBackup=:managerBackup")
            ->setParameter("managerBackup", $user->getTeamId()->getManagerBackup())
            ->getQuery()->getResult();

        $teamsId = [];
        /**
         * @var Team $team
         */
        foreach ($teams as $team) {
            $teamsId []= $team->getId();
        }
        return $teamsId;
    }

    /**
     * @Route("/sourceId-Check/{sourceDestId}",name="sourceDestId_check")
     * @param $sourceDestId
     * @return JsonResponse
     * @throws \Exception
     */
    public function historyCheck($sourceDestId)
    {
        $sourceDest=$this->getDoctrine()->getRepository(Evaluation::class)->findOneBy(['sourceDestID'=>$sourceDestId]);
            $evaHist['evaId']=$sourceDest->getId();
            $evaHist['evaluativeName']=$sourceDest->getEvaluative()->getUserProfile()->__toString();
            $evaHist['evaDate']=$sourceDest->getCreatedAt()->format('d-m-Y H:i:s');
            $evaHist['evaStatu']=$sourceDest->getStatus()->getName();
            $evaHist['evaScore']=$sourceDest->getScore();


        return new JsonResponse($evaHist);
    }

    /**
     * @IsGranted("ROLE_QUALITY_HISTORY")
     * @Route("/history/{evaluation}", name="quality_history")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */

    public function history(Request $request, Evaluation $evaluation)
    {
        $fieldNames = [
            'id' => 'id',
            'score' => 'Skor',
            'status' => 'Durum',
            'evaluative_comment' => 'Değerlendirilen Yorumu',
            'evaluator_comment' => 'Değerlendiren Yorumu',
            'reset_reason_id' => 'Sıfırlama Nedeni',
            'duration' => 'Süre',
            'comment' => 'Yorum',

        ];
        $logRepo = $this->getDoctrine()->getRepository(LogEvaluation::class);

        $logs = array_reverse($logRepo->getLogEntries($evaluation));
        $count=0;
        $status=['','DRAFT','YAYINDA','İTİRAZ','OLUMSUZ','GERİ ÇEVİR','GÜNCELLEME DURUMUNDA','RED','GERİ ÇEVİR','GÜNCELLENDİ','KAYDEDİLDİ'];
        foreach ($logs as $index => $log) {
//
            $oldData = $log->getData();

            if ($index > 0) {
                $datas = $log->getData();


                foreach ($datas as $field => $data) {
                    if ($field!="duration")
                    {
                        $changed['field'] = $field;
                        $changed['fieldHumanRead'] = [$field];
                        if (is_object($data)){
                            if (isset($data->date)){
                                $changed["data"]=$data->format("d-m-Y H:i:s");
                            }

                        }elseif (is_array($data)){
                            if (isset($data["id"])) {

                                if($field=="status")
                                {
                                    $changed["data"] = $status[$data["id"]];
                                }
                                else
                                {
                                    $changed["data"] = $data["id"];
                                }
                            }
                        }
                        else
                            {
                                $changed['data'] = $data;
                             }
                        $changed['username'] = $log->getUsername();
                        $changed['loggedAt'] = $log->getLoggedAt()->format('d-m-Y H:i:s');
                        $history[] = $changed;
                    }
                }
            }
        }

        foreach ($history as $agentEvaluationHistory) {
            $agentField = $agentEvaluationHistory["field"];
        }

        return new JsonResponse($history);
    }

    /**
     * @Route("/user-dest", name="user_dest")
     */
    public function userDesc()
    {
        $evaluations = $this->getDoctrine()->getRepository(Evaluation::class)->findAll();
        $userDest = [];
        foreach ($evaluations as $evaluation){
            $roleGroups = $evaluation->getEvaluative()->getGroups()->toArray();
            /**
             * @var Group $roleGroup
             */
            foreach ($roleGroups as $roleGroup){
                foreach ($roleGroup->getRoles() as $value){
                    $userDest [$evaluation->getEvaluative()->getId()]["role"][] = $value;
                }
            }
            $userDest [$evaluation->getEvaluative()->getId()]["userName"][] = $evaluation->getEvaluative()->getUserProfile()->__toString();
            $userDest [$evaluation->getEvaluative()->getId()]["destID"][] = $evaluation->getSourceDestID();
        }
        return $this->json($userDest);
    }

    /**
     * @Route("/whoEvaluated/{user}", name="who-evaluated")
     */
    public function whoEvaluated(User $user)
    {
        $evaluations=$this->getDoctrine()->getRepository(Evaluation::class);
        $evaluatedArray=[];
        $evaluatedList=$evaluations->createQueryBuilder('e');
        $evaluatedList
            ->select('e AS eva,count(e.id) as counter')
            ->where("e.user=:user")
            ->setParameter("user",$user)
            ->groupBy('e.user,e.evaluative');
        $evaluatedList = $evaluatedList->getQuery()->getResult();

        foreach ($evaluatedList as $evaluatedLists){
            $evaluatedArray[]=$evaluatedLists;
        }
        foreach ($evaluatedArray as $evaluatedArrays )
        {
//            $evaluative=$this->getDoctrine()->getRepository(User::class,$evaluatedArrays['eva']->getEvaluative()->getId());

            if($this->isGranted("ROLE_KALITE_ADMIN") or $this->isGranted("ROLE_KALITE_AGENT") or $evaluatedArrays['eva']->getEvaluative()->getUsername()==$this->getUser()->getUsername())
            {
                $evaCount['evaluative']=$evaluatedArrays['eva']->getEvaluative()->getUsername();
            }
            else
            {
                $evaCount['evaluative']="******* ******";
            }
            $evaCount['evaluativeId']=$evaluatedArrays['eva']->getEvaluative()->getId();
            $evaCount['userId']=$evaluatedArrays['eva']->getUser()->getId();
            $evaCount['status']=$evaluatedArrays['eva']->getStatus()->getName();
            $evaCount['createdAt']=$evaluatedArrays['eva']->getCreatedAt()->format('Y-m-d H:i:s');
            $evaCount['count']=$evaluatedArrays['counter'];
            $evaCount['source']=$evaluatedArrays['eva']->getSourceDestId();
            $evaCount['score']=$evaluatedArrays['eva']->getScore();

            $evaUserCount[]=$evaCount;
//            dump($evaCount);
//            exit();
        }
//        dump($evaUserCount);
//        exit();
        return $this->json($evaUserCount);
    }

    /**
     * @Route("/whoMuchEvaluated/{user}/{evaluative}", name="who-much-evaluated")
     * @param User $user
     * @param $evaluative
     * @return JsonResponse
     */
    public function whoMuchEvaluated(User $user,$evaluative)
    {
        $evaluations=$this->getDoctrine()->getRepository(Evaluation::class);
        $evaluatedList=$evaluations->createQueryBuilder('e');
        $evaluatedResult=$evaluatedList
            ->where("e.user=:user")
            ->andWhere("e.evaluative=:evaluative")
            ->setParameters([
                "user"=>$user,
                'evaluative'=>$evaluative
            ])->getQuery()->getResult();

        foreach ($evaluatedResult as $evaluatedResults){

//            if()
            $source=explode("+",$evaluatedResults->getSourceDestId());
            if(isset($source[1]))
            {
                $lastSource=(int)$source[1];
                $phoneSource=$this->getDoctrine()->getRepository(Calls::class)->findOneBy(['idx'=>$lastSource]);
                $evaCount['source']=$phoneSource->getClid().'--'.$phoneSource->getExten().' at '.$phoneSource->getDt()->format('Y-m-d H:i:s');
            }
            else
            {
                $evaCount['source']=$evaluatedResults->getSourceDestId();
            }

            $evaCount['evaluationId']=$evaluatedResults->getId();
            $evaCount['formName']=$evaluatedResults->getFormTemplate()->getTitle();
            $evaCount['evaluative']=$evaluatedResults->getEvaluative()->getUsername();
            $evaCount['status']=$evaluatedResults->getStatus()->getName();
            $evaCount['createdAt']=$evaluatedResults->getCreatedAt()->format('Y-m-d H:i:s');;
            $evaCount['score']=$evaluatedResults->getScore();

            $evaUserCount[]=$evaCount;

        }
        return $this->json($evaUserCount);
    }

    /**
     * @IsGranted("ROLE_QUALITY_INBOUND")
     * @Route("/", name="quality_inbound")
     * Lists all post entities.
     * @Method("GET")
     * @param Request $request
     * @param DatatableFactory $datatableFactory
     * @param DatatableResponse $responseService
     * @return Response
     * @throws \Exception
     */
    public function inboundAction(Request $request, DatatableFactory $datatableFactory, DatatableResponse $responseService, UserInterface $user)
    {
        $isAjax = $request->isXmlHttpRequest();
        /** @var DatatableInterface $datatable */
        $datatable = $datatableFactory->create(InboundCallDatatable::class);
        $datatable->buildDatatable();

        $datatable2 = $datatableFactory->create(EvaluationSummaryDatatable::class);
        $datatable2->buildDatatable();
        $calls = $this->getDoctrine()->getRepository(Calls::class);
        $evaluation = $this->getDoctrine()->getRepository(Evaluation::class);
        $evaluationResult = [];
        $callsResult=[];
        if ($isAjax) {

            $responseService->setDatatable($datatable);
            $datatableQueryBuilder = $responseService->getDatatableQueryBuilder();
            $qb = $datatableQueryBuilder->getQb();

            if ($this->isGranted("ROLE_KALITE_ADMIN") or $this->isGranted("ROLE_KALITE_AGENT")) {
                $qb->where("calls.user is not null")
                    ->andWhere('calls.callStatus=:callStatus')
                    ->setParameter('callStatus','Done')
                    ->orderBy("calls.dt", "DESC");
            }
            else if ($this->isGranted("ROLE_TAKIM_LIDERI")) {
                $userTeam = $this->getDoctrine()->getRepository(User::class);
                $teamUser = $this->getDoctrine()->getRepository(Team::class);
                $userTeamResult = [];
                $userResult=[];
                $userTeamLeader = $userTeam->createQueryBuilder("u");
                $teamUserLeader = $teamUser->createQueryBuilder("t");
                $userTeamLeader
                    ->where(
                        $userTeamLeader->expr()->in("u.teamId",
                            $teamUserLeader
                                ->select("t.id")
                                ->where("t.manager=:manager")
                                ->orWhere("t.managerBackup=:managerBackup")
                                ->getDQL()
                        )
                    )
                    ->setParameter("manager", $this->getUser())
                    ->setParameter("managerBackup", $this->getUser());

                $userTeamLeader = $userTeamLeader->getQuery()->getResult();

                if (count($userTeamLeader) > 0) {
                    foreach ($userTeamLeader as $item) {
                        $userTeamResult [$item->getId()] = $item;
                       $userResult[]= $item->getId();
                    }
                }
                $qb->where("calls.user is not null")
                    ->andWhere(
                        $qb->expr()->in("calls.user", $userResult)
                    )
                    ->andWhere('calls.callStatus=:callStatus')
                    ->setParameter('callStatus','Done')
                    ->orderBy("calls.dt", "DESC");

            }
//            $qb->where('queuelog.event  in(:event)')->setParameter9('event', ['COMPLETEAGENT', 'COMPLETECALLER']);
            return $responseService->getResponse();
        }

        return $this->render('@IbbEvalutionManagement/quality/index.html.twig', array(
            'datatable' => $datatable,
            'datatable2' => $datatable2,
            /**
             * @var User $user
             */
            'manager' => $user,
        ));
    }

    /**
     * @IsGranted("ROLE_EVALUATION_LIST")
     * @Route("/EvaluationList", name="evaluation_list")
     * Lists all post entities.
     * @Method("GET")
     * @param UserInterface $user
     * @param Request $request
     * @param DatatableFactory $datatableFactory
     * @param DatatableResponse $responseService
     * @return Response
     * @throws \Exception
     */
    public function evaluationList(Request $request, DatatableFactory $datatableFactory,
                                   DatatableResponse $responseService, UserInterface $user)
    {

        $isAjax = $request->isXmlHttpRequest();
        /** @var DatatableInterface $datatable */
        $datatable = $datatableFactory->create(EvaluationListDatatable::class);
        $datatable->buildDatatable();

        $datatable2 = $datatableFactory->create(EvaluationSummaryDatatable::class);
        $datatable2->buildDatatable();

        if ($isAjax) {
            $responseService->setDatatable($datatable);
            $datatableQueryBuilder = $responseService->getDatatableQueryBuilder();

            $qb = $datatableQueryBuilder->getQb();
             if($this->isGranted('ROLE_KALITE_ADMIN') or $this->isGranted('ROLE_KALITE_AGENT') )
             {
                $qb->orderBy('evaluation.id','DESC');
             }
             /**
              * @todo Takım lideri kaliteden sadece yayında olanları görebilicek
              */
             else if ($this->isGranted('ROLE_TAKIM_LIDERI'))
              {
                $em = $this->getDoctrine()->getManager();
                $teamRepo = $em->getRepository(Team::class);
                $evaRepo=$em->getRepository(Evaluation::class);
                $statusRepo=$em->getRepository(EvaluationStatus::class);

                $qb
                    ->leftJoin("evaluation.user", "u")
                    ->where(
                        $qb->expr()->in(
                            "u.teamId",
                            $teamRepo->createQueryBuilder("t")
                                ->select("t.id")
                                ->where("t.manager=:manager")
                                ->orWhere("t.managerBackup=:managerBackup")
                                ->getQuery()->getDQL()
                        )
                    )
                    ->andwhere("evaluation.status!=1")
                    ->andWhere("evaluation.status!=10")
                    ->orWhere("evaluation.evaluative=:evaluative")
                    ->setParameters([
                        "manager" => $this->getUser(),
                        "managerBackup" => $this->getUser(),
                        "evaluative" => $this->getUser(),
//                        "status1" => 1,
//                        "status2" => 10,
                    ])
                    ->orderBy('evaluation.id','DESC') ;
              }
            else if($this->isGranted('ROLE_AGENT'))
            {
                $qb
                    ->Where("evaluation.user=:user")
                    ->andwhere("evaluation.status!=1")
                    ->andWhere("evaluation.status!=10")
                    ->orderBy('evaluation.id','DESC')
                    ->setParameter("user",$user);
            }

            return $responseService->getResponse();
        }

        if(!$this->isGranted('ROLE_KALITE_AGENT'))
        {
            $datatable2=null;
        }

        return $this->render('@IbbEvalutionManagement/quality/evaluationList.html.twig', array(
            'datatable' => $datatable,
            'datatable2' => $datatable2,
            'memberLogin'=>$user->getId()

        ));
    }

    /**
     * @IsGranted("ROLE_EVALUATION_OBJECTION")
     * @Route("/EvaObjection", name="evaluation_objection")
     * Lists all post entities.
     * @Method("GET")
     * @param UserInterface $user
     * @param Request $requedst
     * @return Response
     * @throws \Exception
     */
    public function evaluationObjection(Request $request, UserInterface $user)
    {


        return $this->render('@IbbEvalutionManagement/quality/evaluationList.html.twig', array(
            'datatable' => "test",
        ));
    }

    /**
     * @Route("evaluationDelete/{id}", name="evaluation_delete")
     *
     */
    public function delete(Request $request, Evaluation $evaluation): Response
    {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($evaluation);
            $entityManager->flush();
        return $this->redirectToRoute('evaluation_list');
    }

    /**
     * @IsGranted("ROLE_QUALITY_OUTBOUND")
     * @Route("/outbound", name="quality_outbound")
     * Lists all post entities.
     * @Method("GET")
     * @param Request $request
     * @param DatatableFactory $datatableFactory
     * @param DatatableResponse $responseService
     * @return Response
     * @throws \Exception
     */

    public function outboundAction(Request $request, DatatableFactory $datatableFactory, DatatableResponse $responseService)
    {

        $isAjax = $request->isXmlHttpRequest();
        /** @var DatatableInterface $datatable */
        $datatable = $datatableFactory->create(OutboundCallDatatable::class);
        $datatable->buildDatatable();
        if ($isAjax) {
            $responseService->setDatatable($datatable);
            $datatableQueryBuilder = $responseService->getDatatableQueryBuilder();
            $qb = $datatableQueryBuilder->getQb();
            $qb->where('cdr.amaflags=:amaflags')
                ->andWhere('cdr.userfield is not null')
                ->setParameters(["amaflags" => "2"]);

            return $responseService->getResponse();
        }
        return $this->render('@IbbEvalutionManagement/quality/index.html.twig', array(
            'datatable' => $datatable,
        ));
    }


    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * TestTwig constructor.
     */
    public function __construct(EngineInterface $templating)
    {
        $this->templating = $templating;
    }



    /**
     * @Route("/quality/evaluation/congr-mail/{evaluation}", name="quality_evalutation_congr_mail")
     * @param $evaluation
     * @param \Swift_Mailer $mailer
     * @return JsonResponse
     */
    public function sendMailCongr(Evaluation $evaluation, \Swift_Mailer $mailer)
    {
        try {
            $user = $evaluation->getUser();
            $agentMail = $user->getEmail();
            $evaluationID=$evaluation->getId();
            $agentFullName = $user->getUserProfile()->__toString();
            $teamLeader = $user->getTeamId()->getManager()->getEmail();
            $message = (new \Swift_Message(' Tebrikler !'))
                ->setFrom('153santralbildirim@ibb.gov.tr')
                ->setTo([$teamLeader, $agentMail,"dl.153pys@ibb.gov.tr"])
                ->setBody(
                    $this->renderView('mail/congrMail.html.twig',
                        [
                            'name' => $agentFullName,
                            'evaluationId'=>$evaluationID

                        ]),
                    'text/html')
                ->setCharset('utf-8');
            $mailResult = $mailer->send($message);

        } catch (\Exception $exception) {
            return $this->json(['fredy' => 'ohno']);
        }
        return $this->json(['fredy' => 'crugger']);
    }

    /**
     * @Route("/quality/evaluation/feedback-mail/{evaluation}",name="quality_evaluation_feedback_mail")
     * @param Evaluation $evaluation
     * @param \Swift_Mailer $mailer
     * @return JsonResponse
     */
    public function sendMailFeedBack(Evaluation $evaluation, \Swift_Mailer $mailer)
    {
        try {

            $user = $evaluation->getUser();
            $agentMail = $user->getEmail();
            $evaluationID=$evaluation->getId();
            $agentFullName = $user->getUserProfile()->__toString();
            $teamLeader = $user->getTeamId()->getManager()->getEmail();
            $message = (new \Swift_Message(' Geri Bildirim !'))
                ->setFrom('153santralbildirim@ibb.gov.tr')
                ->setTo([$teamLeader, $agentMail,"dl.153pys@ibb.gov.tr"])
                ->setBody(
                    $this->renderView('mail/feedbackMail.html.twig',
                        [
                            'name' => $agentFullName,
                            'evaluationId'=>$evaluationID
                        ]),
                    'text/html')
                ->setCharset('utf-8');

            $mailResult = $mailer->send($message);

        } catch (\Exception $exception) {
            return $this->json(['fredy' => 'ohno']);
        }
        return $this->json(['fredy' => 'crugger']);


    }

    /**
     * @Route("/extraSourceAdd", name="extra_source_add")
     * @param Request $request
     * @param EvaluationExtraSourceRepository $sourceRepository
     * @return JsonResponse
     * @throws \Exception
     */
    public function extraSourceAdd(Request $request,EvaluationExtraSourceRepository $sourceRepository)
    {
        $em = $this->getDoctrine()->getManager();

        $extraSource = $request->get('extraSource');
        $description = $request->get('description');
        $evaluationId = $request->get('evaluationId');

        $result=[];

        $source=$sourceRepository->findOneBy(['source'=>$extraSource,'evaluation'=>$evaluationId]);
        if(is_null($source))
        {
            $extraSourceNew=new EvaluationExtraSource();
            $extraSourceNew
                ->setEvaluation($em->find(Evaluation::class,$evaluationId))
                ->setSource($extraSource)
                ->setDescription($description);

            $em->persist($extraSourceNew);
            $em->flush();

           $result['success']=true;
           $result['data']=$description;

        }
        else
        {
            $result['success']=false;
            $result['data']=$description;
        }
        return new JsonResponse($result);

    }

    /**
     * @Route("extraSourceRemove/{source}", name="extra_source_remove")
     * @param Request $request
     * @param $source
     * @return JsonResponse
     */
    public function extraSourceRemove(Request $request,$source)
    {

        $em = $this->getDoctrine()->getManager();
        $data= $em->getRepository(EvaluationExtraSource::class)->findBy(['source'=> $source]);

        $em->remove($data[0]);
        $em->flush();
        return new JsonResponse('Silindi');
    }


    /**
     * @Route("/extraSourceList/{evaluation}", name="extra_source_list")
     * @param Request $request
     * @param Evaluation $evaluation
     * @return JsonResponse
     */
    public function extraSourceList(Request $request,Evaluation $evaluation)
    {
        $logRepo = $this->getDoctrine()->getRepository(EvaluationExtraSource::class);
        $extraSource=$logRepo->findAll();

        foreach ($extraSource as $extra)
        {
            $extraNames['source'] = $extra->getSource();
            $extraNames['description'] = $extra->getDescription();
            $extraNames['evaluation'] = $extra->getEvaluation()->getId();
            $extras[] = $extraNames;
        }
        return new JsonResponse($extras);

    }

    /**
     * @IsGranted("ROLE_QUALITY_EVALUTATION")
     * @Route("/evaluation/{evaluationId}", name="quality_evalutation")
     * @param UserInterface $loginUser
     * @param Request $request
     * @param Evaluation $evaluationId
     * @return Response
     *
     */

    public function evaluation(UserInterface $loginUser, Request $request,Evaluation $evaluationId)
    {

        $em = $question = $this->getDoctrine()->getManager();

        $audioUrl = 0;
        $userField = "";
        $buttons = [];


        $evaluation = $em->getRepository(Evaluation::class)
            ->findOneBy(
                [
                    'id'=>$evaluationId
                ] );
        $holdDuration=0;

        if(strstr($evaluation->getSourceDestID(), "+"))
        {
            $ids=explode("+",$evaluation->getSourceDestID());
            if($ids!=null)
            {
                $call = $em->getRepository(Calls::class)->findOneBy(['idx'=>$ids[1]]);
                $holds=$em->getRepository(HoldLog::class)->findBy([
                    'uniqueId'=>$call->getUid2()
                ]);
                foreach ($holds as $hold)
                {
                    $holdDuration+=$hold->getDuration();
                }
            }
        }


        $sourceID=$evaluation->getSourceDestID();

        $source=$evaluation->getSource();
        $user=$evaluation->getUser();
        $formTemplate=$evaluation->getFormTemplate();

        $teamManagerOfUser = $evaluation->getUser()->getTeamId()->getManager()->getUserProfile()->getFirstName() . " " . $evaluation->getUser()->getTeamId()->getManager()->getUserProfile()->getLastName();
        $evaUpdateDate = $evaluation->getUpdatedAt()->format('d-m-Y H:i:s');
        $evaCreateDate = $evaluation->getCreatedAt()->format('d-m-Y H:i:s');
        $agentStatusName = $evaluation->getStatus()->getName();

        if ($evaluation->getResetReason() == null) {
            $agentResetReason = "Boş";
            $resetReason=0;
        }
        else {
            $agentResetReason = $evaluation->getResetReason()->getName();
            $resetReason=$evaluation->getResetReason()->getId();
        }


//        $reset = $em->getRepository(EvaluationResetReason::class)->createQueryBuilder("e")
//            ->where("e.forms")
//        dump($reset);
//        exit();

        $evaluation->ressetFormId = [];

        $form = $this->createForm(EvaluationFormType::class, $evaluation);


        $form->handleRequest($request);

        if ($evaluation->getStatus() != null) {

            if ($this->isGranted('ROLE_KALITE_AGENT')) {
                $buttons['save'] = true;
                $buttons['saveAndPublish'] = true;
                $buttons['remove'] = true;
                $buttons['unPublish'] = false;
                $buttons['max'] = true;

            }
            if($this->isGranted('ROLE_SUPERVISOR'))
            {
                $buttons['save'] = false;
                $buttons['saveAndPublish'] = false;
                $buttons['remove'] = false;
                $buttons['unPublish'] = false;
                $buttons['max'] = false;
            }
            if($this->isGranted('ROLE_SUPERVISOR') and $evaluation->getStatus()->getId() == 3 )
            {
                $buttons['save'] = false;
                $buttons['saveAndPublish'] = false;
                $buttons['remove'] = false;
                $buttons['unPublish'] = false;
                $buttons['max'] = false;
                $buttons['rejectAccept'] = true;
                $buttons['rejectDeny'] = true;
                $buttons['rejectComment'] = true;
            }

            if ($this->getUser() == $evaluation->getUser() and $evaluation->getStatus()->getId() == 2 ) {
                    $buttons['reject'] = true;
            }
            if ($this->getUser() == $evaluation->getUser() and $evaluation->getStatus()->getId() == 4 ) {
                    $buttons['secondReject'] = true;
            }

            if ($this->isGranted('ROLE_KALITE_AGENT') ) {
                if (in_array($evaluation->getStatus()->getId(), [10, 1])) {
                    $buttons['save'] = true;
                    $buttons['saveAndPublish'] = true;
                    $buttons['max'] = true;
                    $buttons['remove'] = true;
                }
            }
            if ($this->getUser() == $evaluation->getEvaluative() or $this->isGranted('ROLE_TAKIM_LIDERI') ) {
                if (in_array($evaluation->getStatus()->getId(), [10, 1])) {
                    $buttons['save'] = true;
                    $buttons['saveAndPublish'] = true;
                    $buttons['max'] = true;
                }
            }
            if ($evaluation->getStatus()->getId() == 2  and $this->isGranted('ROLE_KALITE_AGENT') ) {
                $buttons['save'] = false;
                $buttons['saveAndPublish'] = false;
                $buttons['remove'] = true;
                $buttons['unPublish'] = true;
                $buttons['max'] = false;
            }
            if ($evaluation->getStatus()->getId() == 3  and $this->isGranted('ROLE_KALITE_AGENT') ) {
                $buttons['save'] = false;
                $buttons['saveAndPublish'] = false;
                $buttons['remove'] = true;
                $buttons['unPublish'] = true;
                $buttons['max'] = false;
            }
            if ($evaluation->getStatus()->getId() == 3 and ($evaluation->getUser()->getTeamId()->getManager() == $this->getUser() || $evaluation->getUser()->getTeamId()->getManagerBackup() == $this->getUser())) {
                $buttons['rejectAccept'] = true;
                $buttons['rejectDeny'] = true;
                $buttons['rejectComment'] = true;
            }

            if ($evaluation->getStatus()->getId() == 4  and $this->isGranted('ROLE_KALITE_AGENT') ) {
                $buttons['save'] = false;
                $buttons['saveAndPublish'] = false;
                $buttons['remove'] = true;
                $buttons['unPublish'] = true;
                $buttons['max'] = false;
            }
            if ($evaluation->getStatus()->getId() == 5 and $this->isGranted('ROLE_KALITE_AGENT')) {
                $buttons['reEvaluate'] = true;
                $buttons['secondReEvaluate'] = true;
                $buttons['save'] = false;
                $buttons['saveAndPublish'] = false;
                $buttons['remove'] = false;
                $buttons['unPublish'] = false;
                $buttons['max'] = false;
            }
            if ($evaluation->getStatus()->getId() == 6  and $this->isGranted('ROLE_KALITE_AGENT') ) {
                $buttons['save'] = false;
                $buttons['upgrade'] = true;
                $buttons['remove'] = false;
                $buttons['unPublish'] = false;
                $buttons['saveAndPublish'] = false;
                $buttons['max'] = true;

            }
            if ($evaluation->getStatus()->getId() == 7  and $this->isGranted('ROLE_KALITE_AGENT') ) {
                $buttons['save'] = false;
                $buttons['saveAndPublish'] = false;
                $buttons['remove'] = false;
                $buttons['unPublish'] = true;
                $buttons['max'] = false;
            }
    //            if ($evaluation->getStatus()->getId() == 8 and $this->isGranted('ROLE_KALITE_AGENT')) {
    //                $buttons['reEvaluate'] = true;
    //                $buttons['secondReEvaluate'] = true;
    //                $buttons['rejectComment'] = true;
    //                $buttons['rejectCloseComment'] = true;
    //                $buttons['unPublish'] = true;
    //                $buttons['save'] = false;
    //                $buttons['saveAndPublish'] = false;
    //                $buttons['max'] = false;
    //            }
            if ($evaluation->getStatus()->getId() == 9 and $this->isGranted('ROLE_KALITE_AGENT')) {
                $buttons['unPublish'] = true;
            }

            if ($request->getMethod() == 'POST') {

//                dump($em->find(EvaluationReasonType::class,$request->request->get("evaluation_form")["evaluationReasonType"]));
//                dump($request->request->all());
//                exit();
//
                $post = $request->request->all();
//                $agentStatusName = $em->find(EvaluationStatus::class, $post["evaluation_form"]["status"])->getName();
                $eaRepo = $em->getRepository(EvaluationAnswer::class);
                $agentStatusName = $evaluation->getStatus()->getName();

                if ($evaluation->getResetReason() == null) {
                    $agentResetReason = "Boş";
                }
                else {
                    $agentResetReason = $evaluation->getResetReason()->getName();
                }

                $evaluation
                    ->setScore($request->get('score'))
                    ->setComment($request->request->get('evaluation_form')["comment"])
                    ->setEvaluationReasonType($em->find(EvaluationReasonType::class,$request->request->get("evaluation_form")["evaluationReasonType"]))
                     ;

//                    ->addEvaluationExtraSource($request->request->get('evaluation_form')["evaluationExtraSources"])
//                    ->setRejectComment($request->request->get('evaluation_form')["rejectComment"])
//                    ->setRejectCloseComment($request->request->get('evaluation_form')["rejectCloseComment"])
            }
            if (is_array($request->get('answer'))) {

                foreach ($request->get('answer') as $questionID => $optionID) {
                    $question = $em->find(FormQuestion::class, $questionID);
                    $option = $em->find(FormQuestionOption::class, $optionID);

                    $oldAnswer = $eaRepo->findOneBy(['evaluation' => $evaluation, 'question' => $question]);
                    if ($oldAnswer instanceof EvaluationAnswer) {
                        $answer = $oldAnswer;
                    } else {
                        $answer = new EvaluationAnswer();
                    }
                    $answer->setEvaluative($this->getUser())
                        ->setQuestion($question)
                        ->setAnswer($option);

                    $evaluation->addEvaluationAnswer($answer);
                }

            }
//            dump($request->request);
//            exit();
//            $extraSource = $request->request->get('evaluation_form')["evaluationExtraSources"];
//            if (is_array($extraSource)) {
//                foreach ($extraSource as $source) {
//                    $oldSource = $em->getRepository(EvaluationExtraSource::class)->findOneBy(['evaluation' => $evaluation, 'source' => $source]);
//
//                    if (is_null($oldSource)) {
//                        $oldSources = new EvaluationExtraSource();
//                        $oldSources->setEvaluation($evaluation)
//                            ->setSource($source);
//                        $em->persist($oldSources);
//                        $em->flush();
//                    }
//                }
//            }
            $em->persist($evaluation);
            $em->flush();
        }
        $answers = $evaluation->getEvaluationAnswers();
        $answerArray = [];
        foreach ($answers as $answer) {
            $answerArray[] = $answer->getAnswer()->getId();
        }


        $formView = $form->createView();

        $queueId=explode("+",$sourceID);

//        $call = $this->getDoctrine()->getManager()->getRepository(Calls::class)
//            ->findOneBy(['queueCallId' => $queueId[0], 'whoCompleted' => ['COMPLETEAGENT', 'COMPLETECALLER']]);
//
        $callQuery = $this->getDoctrine()->getManager()->getRepository(Calls::class)->createQueryBuilder('c')
            ->where('c.callId=:callId')
            ->orWhere('c.queueCallId=:queueCallId')
            ->setParameters([
                'callId'=>$queueId[0],
                'queueCallId'=>$queueId[0],
            ])->getQuery()->getResult();
        if($callQuery)
        {
            $call=$callQuery[0];
        }
        else
        {
            $call=null;
        }
        if ($call instanceof Calls) {

            $audioUrl = $this->generateUrl('voice_record', ['call' => $call->getIdx()]);


            $userField = $call->getUserfield();
            $evaluation->setPhoneNumber($call->getClid())
                ->setCallDate($call->getDt())
                ->setDuration($call->getDurExten());

        }
        if ($userField!=null && $this->isGranted('ROLE_KALITE_ADMIN'))
        {
            $newUserField=$userField;
        }
        else
        {
            $newUserField=null;
        }

        return $this->render("@IbbEvalutionManagement/quality/evaluation.html.twig", [
                'formTemplate' => $formTemplate,
                'form' => $formView,
                'user' => $user,
                'source' => $source,
                'sourceID' => $sourceID,
                'isCall' => $newUserField,
                'answerArray' => $answerArray,
                'userField' => $userField,
                'evaluation' => $evaluation,
                'audioUrl' => $audioUrl,
                'teamManagerName' => $teamManagerOfUser,
                'agentStatus' => $agentStatusName,
                'evaUpdateDate' => $evaUpdateDate,
                'evaCreateDate' => $evaCreateDate,
                'agentResetReason' => $agentResetReason,
                'userID'=>$evaluation->getUser()->getId(),
                'memberLogin'=>$loginUser->getId(),
                'evaluative'=>$evaluation->getEvaluative(),
                'evaluativeID'=>$evaluation->getEvaluative()->getId(),
                'statusID'=>$evaluation->getStatus()->getId(),
                'callDate'=>$evaluation->getCallDate()->format('d-m-Y H:i:s'),
                'citizenID'=>$evaluation->getCitizenId(),
                'reasonType'=>$evaluation->getEvaluationReasonType(),
                'evaluationType'=>$evaluation->getEvaluationType(),
                'phoneNumber'=>$evaluation->getPhoneNumber(),
                'holdDuration'=>$holdDuration
            ] + $buttons);
    }

    /**
     * @IsGranted("ROLE_EVALUATION_REJECTION")
     * @Route("/EvaRejection/{evaluationId}", name="evaluation_rejection")
     * Lists all post entities.
     * @Method("GET")
     * @param UserInterface $user
     * @param Request $request
     * @param Evaluation $evaluationId
     * @return Response
     * @throws \Exception
     */
    public function evaluationRejection(Request $request, UserInterface $user, Evaluation $evaluationId)
    {
        $em = $this->getDoctrine()->getManager();
        $evaluationId->setStatus($em->find(EvaluationStatus::class, 3));
        $em->persist($evaluationId);
        $em->flush();
        return $this->redirectToRoute("evaluation_list");
    }

    /**
     * @IsGranted("ROLE_QUALITY_NEW")
     * @Route("/newx/{callid}/{callType}", name="quality_new")
     * @param Request $request
     * @param null $callid"
     * @param null $callType
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function new(Request $request, $callid = null, $callType = null)
    {
        $em = $this->getDoctrine()->getManager();
        $evaluation = new Evaluation();
        $session = $this->get('session');

        $teamID=0;
        if ($session->has('prevQualityFormData')) {
            $oldevaluation = $session->get('prevQualityFormData');

            $user = $this->getDoctrine()->getRepository(UserProfile::class)
                ->findOneBy(['user' => $oldevaluation->getUser()])->getUser();
                $teamID=$user->getTeamId()->getId();
//                if($teamID==null)
//                {
//                    $teamID=0;
//                }
//                dump($teamID);
//                exit();
            if($oldevaluation->getSource())
            {
                $evaluation->setSource($em->find(EvaluationSource::class, $oldevaluation->getSource()->getId()));
            }
            if($oldevaluation->getFormTemplate()->getId())
            {
                $evaluation->setFormTemplate($em->find(FormTemplate::class, $oldevaluation->getFormTemplate()->getId()));
            }

        }

        if ($callid) {
//          $call = $this->getDoctrine()->getManager('asterisk')->getRepository(QueueLog::class)
//                ->findOneBy(['callid' => $callid, 'event' => ['COMPLETEAGENT', 'COMPLETECALLER']]);
//

            $ids=explode("+",$callid);

            $call = $this->getDoctrine()->getManager()->getRepository(Calls::class)->findOneBy(['idx'=>$ids[1]]);

            if(!$evaluation->getSourceDestID())
                $evaluation->setSourceDestID($callid);


            if ($call instanceof Calls) {

                $evaluation->setUser($call->getUser());
                $evaluation->setSourceDestID($callid);
                $evaluation->setSource($this->getDoctrine()->getRepository(EvaluationSource::class)->find(1));

//                if(is_null($call->getQueueCallId()))  {
//                    $evaluation->setSourceDestID($call->getCallId());
//                }
//                else{
//                    $evaluation->setSourceDestID($call->getQueueCallId());
//                }

            } else {
                $cdr = $this->getDoctrine()->getRepository(Cdr::class)->findOneBy(['uniqueid' => $callid]);

                if ($cdr instanceof Cdr) {

                    /**
                     * @todo arama sabit telefondan yapılmışsa burası patlar? neden böyle yapıldı?
                     */
                    $user = $this->getDoctrine()->getRepository(UserProfile::class)->findOneBy(['extension' => $cdr->getSrcOrg()])->getUser();

                    $evaluation->setUser($user);
                    $evaluation->setSourceDestID($cdr->getUniqueid());
                    $evaluation->setSource($this->getDoctrine()->getRepository(EvaluationSource::class)->find(1));
                }
            }
        }

        $form = $this->createForm(EvaluationType::class, $evaluation);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $session->set('prevQualityFormData', $form->getData());
            $evaluation->setSource($form->getViewData()->getSource());
//            $evaluation->setFormTemplate($form->getViewData()->getFormtemplate());


            if($request->request->get('evaluation')['source']== 4 or $request->request->get('evaluation')['source']== 5 )
            {
                $evaluationControl=$em->getRepository(Evaluation::class)->findBy([
                    'sourceDestID'=>$evaluation->getSourceDestID(),
                    'user'=>$evaluation->getUser()->getId(),
                ]);
            }
            else
            {
                $evaluationControl=$em->getRepository(Evaluation::class)->findBy([
                    'sourceDestID'=>$evaluation->getSourceDestID(),
                ]);
            }

            if(!$evaluationControl)
            {
                $ids=explode("+",$request->request->get('evaluation')['sourceDestID']);


                $user=$em->find(User::class,$request->request->get('evaluation')['user']);
                $callid = $this->getDoctrine()->getManager()->getRepository(Calls::class)->createQueryBuilder('c')
                    ->where('c.callId=:callId')
                    ->orWhere('c.queueCallId=:queueCallId')
                    ->setParameters([
                        'callId'=>$ids[0],
                        'queueCallId'=>$ids[0]
                    ])->getQuery()->getResult();

                if($callid!=null)
                {
                    $callDate=$callid[0]->getDt();
                    $duration= $callid[0]->getdurExten();
                    $phoneNumber=$callid[0]->getclid();
                }
                else
                {
                    $callDate=new \DateTime();
                    $duration=0;
                    $phoneNumber='0';
                }

                $formTemplate=$em->find(FormTemplate::class,$request->request->get('evaluation')['formTemplate']);
                $reasonType=$em->find(EvaluationReasonType::class, 1);

                if($formTemplate->getId()==15)
                {
//                    $formTemplate=$em->find(FormTemplate::class,243);
                    $reasonType=$em->find(EvaluationReasonType::class, 5);
                }
                elseif ($formTemplate->getId()==166)
                {
//                    $formTemplate=$em->find(FormTemplate::class,237 );
                    $reasonType=$em->find(EvaluationReasonType::class, 5);
                }
                elseif ($formTemplate->getId()== 264)
                {
//                    $formTemplate=$em->find(FormTemplate::class,242);
                    $reasonType=$em->find(EvaluationReasonType::class, 5);
                }

                $source=$em->find(EvaluationSource::class,$request->request->get('evaluation')['source']);
                $evaluation
                    ->setUser($user)
                    ->setFormTemplate($formTemplate)
                    ->setSource($source)
                    ->setCreatedAt(new \DateTime())
                    ->setCitizenID($request->request->get('evaluation')['citizenID'])
                    ->setSourceDestID($request->request->get('evaluation')['sourceDestID'])
                    ->setScore(0)
                    ->setPhoneNumber($phoneNumber)
                    ->setEvaluative($this->getUser())
                    ->setStatus($em->find(EvaluationStatus::class, 1))
                    ->setEvaluationReasonType($reasonType)
                    ->setDuration($duration)
                    ->setCallDate($callDate);

                $call = $this->getDoctrine()->getManager()->getRepository(Calls::class)
                    ->findOneBy(['queueCallId' => $request->request->get('sourceDestID'), 'whoCompleted' => ['COMPLETEAGENT', 'COMPLETECALLER']]);


                if ($call instanceof Calls) {
                    $audioUrl = $this->generateUrl('voice_record', ['call' => $call->getIdx()]);
                    $userField = $call->getUserfield();

                    $evaluation->setPhoneNumber($call->getclid())
                        ->setCallDate($call->getDt())
                        ->setDuration($call->getDurExten());
                }

                $em->persist($evaluation);
                $em->flush();
                return $this->redirectToRoute('quality_evalutation', [
                        'evaluationId'=>$evaluation->getId()
                    ]
                );
            }
            else
            {
                return $this->render("@IbbEvalutionManagement/quality/new.html.twig", ['form' => $form->createView(),
                    'hasError'=>'Bu kayıt mevcut tekrar oluşturamazsınız']);

//                if($form->getViewData()->getFormtemplate()==$evaluationControl[0]->getFormTemplate()->getTitle())
//                {
//                    return $this->redirectToRoute('quality_evalutation', [
//                            'evaluationId'=>$evaluationControl['0']->getId()
//                        ]
//                    );
//                }
//                else
//                {
//
//                }
            }
        }

        if ($evaluation->getSource()==null)
        {
            $sourceState=null;
        }
        else
        {
            $sourceState=$evaluation->getSource()->getId();
        }
        return $this->render("@IbbEvalutionManagement/quality/new.html.twig", [
            'form' => $form->createView(),
            'teamID'=>$teamID,
            'callID'=>$callid]);
    }



    /**
     * @Route("/listen-calls",name="listen_calls")
     * @return Response
     */
    public function listenCalls()
    {

        return $this->render("@IbbEvalutionManagement/quality/listen.html.twig");
    }

    /**
     * @Route("/listen-call-search/{clid}",name="listen_call_search")
     * @param $clid
     * @return JsonResponse
     */
    public function listenCallSearch($clid)
    {
        $call=$this->getDoctrine()->getRepository(Calls::class)->findBy(['clid'=>[$clid,'9'.$clid]]);

        $callDt=[];
        foreach ($call as $calls)
        {

            $callDetail["callDate"]=$calls->getDt();
            $callDetail["callId"]=$calls->getIdx();
            if($calls->getUser()==null)
                $agentName="Boş";
            else
            $callDetail["agentName"]=$calls->getUser()->getUserProfile()->__toString();

            $callDetail["phoneNumber"]=$calls->getClid();
            $callDetail["agent"]=$calls->getQueue();
            $callDetail["duration"]=$calls->getDurExten();
            $callDt[]=$callDetail;

        }

        return $this->json($callDt);
    }

    /**
     * @IsGranted("ROLE_EVALUATED_CALLS")
     * @return Response
     * @Route("/evaluated-calls", name="evaluated_calls")
     */
    public function evaluatedCalls()
    {
        return $this->render("@IbbEvalutionManagement/quality/evaluatedCalls.html.twig");
    }

    /**
     * @return Response
     * @Route("/evaluation-tracking", name="evaluation_tracking")
     */
    public function evaluationTracking()

    {
        return $this->render("@IbbEvalutionManagement/quality/evaluationTracking.html.twig");
    }

    /**
     * @return Response
     * @Route("/representative-objection", name="representative_objection")
     */
    public function representativeObjection()

    {
        return $this->render("@IbbEvalutionManagement/quality/representativeObjection.html.twig");
    }

    /**
     * @Route("/voice-record/{call}", name="voice_record")
     * @param $call
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function voiceRecord(Calls $call, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $recordLog = new RecordListenLog();

        $recordLog->setClientIp($request->getClientIp());
        $recordLog->setListenTime(new \DateTime());
        $recordLog->setRecord($call);
        $recordLog->setUser($this->getUser());

        $em->persist($recordLog);
        $em->flush();

        $uid = $call->getUserfield();


        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'http://' . $this->getParameter('tbxSipServer') . '/stream.php?uid=' . $uid);
        $content = $response->getBody()->getContents();

        header('Content-type: audio/mpeg');
        header('Content-length: ' . strlen($content));
        header('X-Pad: avoid browser bug');
        Header('Cache-Control: no-cache');
        header("Content-Transfer-Encoding: binary");
        header("Content-Type: audio/mpeg, audio/x-mpeg, audio/x-mpeg-3, audio/mpeg3");
        header('Content-Disposition: filename="' . $uid . '.mp3"');

        echo $content;

        exit;

    }

    /**
     * @Route("/source-add",name="source_add")
     */
    public function sourceAdd()
    {
        $em = $this->getDoctrine()->getManager();

        $evaluationExtraSource = new EvaluationExtraSource();

//        $evaluationExtraSource
//            ->setEvaluationId()

    }

}