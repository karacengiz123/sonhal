<?php
/**
 * Created by PhpStorm.
 * User: cengi
 * Date: 2.05.2019
 * Time: 17:25
 */

namespace App\Controller;


use App\Asterisk\Entity\Cdr;
use App\Asterisk\Entity\QueueLog;
use App\Asterisk\Entity\IvrLogs;
use App\Entity\Calls;
use App\Entity\Evaluation;
use App\Entity\FormCategory;
use App\Entity\LogEvaluation;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\UserProfile;
use App\Helpers\Date;
use r\ValuedQuery\Json;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TeamLeaderProtestCountController extends AbstractController
{


    /**
     * @Route("/teamLeaderProtestCountForm" ,name="team-leader-protest-count")
     * @return Response
     */
    public function teamLeaderProtest(Request $request)
    {
        $logEvoRes = [];
        $teamId = [];

        $em = $this->getDoctrine()->getManager();
        $teams = $em->getRepository(Team::class)->findAll();
        foreach ($teams as $team) {
            $choiceTeam [$team->getName()] = $team->getId();
        }
        $form = $this->createFormBuilder()
            ->add("firstDate", DateType::class, [
                "label" => "İlk Tarih",
                "attr" => ["class" => "form-control"],
                'widget' => 'single_text'
            ])
            ->add("lastDate", DateType::class, [
                "label" => "Son Tarih",
                "attr" => ["class" => "form-control"],
                'widget' => 'single_text'
            ])
            ->add("team", ChoiceType::class, [
                "label" => "Takım Seçiniz",
                'choices' => $choiceTeam,
                "attr" => ["class" => "form-control select2",
                    "multiple" => "multiple"]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Listele'
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $formHandle = $form->getViewData();
            $teamLeaderId = $formHandle["team"];

            $firstDate = $formHandle["firstDate"]->format("Y-m-d 00:00:00");
            $lastDate = $formHandle["lastDate"]->format("Y-m-d 23:59:59");

            $team = $em->find(Team::class, $teamLeaderId);
            $users = $team->getUsers()->toArray();
            $userId = [];
            /**
             * @var User $user
             */
            foreach ($users as $user) {
                $userId [] = $user->getId();
            }


//        $evaluations = $this->getDoctrine()->getRepository(Evaluation::class)->findAll();
            $evaluationRepository = $this->getDoctrine()->getRepository(Evaluation::class);
            /**
             * @var Team $teams
             */
            $evaluatedLists=$evaluationRepository->createQueryBuilder('e');
            $evaluatedLists
                ->where("e.createdAt between :sDate and :eDate")
                ->setParameter("sDate", $firstDate)
                ->setParameter("eDate", $lastDate)
                ->andWhere("e.evaluative=:evaluative")
                ->setParameter("evaluative", $team->getManager()->getId())
                ->andWhere(
                    $evaluatedLists->expr()->in("e.user",$userId)
                )
                ->groupBy('e.user,e.evaluative');
            $evaluatedLists = $evaluatedLists->getQuery()->getResult();
            foreach ($evaluatedLists as $evaluation) {
//            $logEvos = $em->getRepository(LogEvaluation::class)->findBy(["objectId" => $evaluation->getId()]);
//            $logEvos = $em->getRepository(LogEvaluation::class)->findBy(["objectId" => 815]);
                $logEvos = $em->getRepository(LogEvaluation::class)
                    ->createQueryBuilder("le")
                    ->where("le.objectId=:objectId")
                    ->setParameter("objectId", $evaluation->getId())
                    ->getQuery()->getResult();


                $varItiraz = false;
                $varObjectId = 0;
                $countProtest = 0;
                $countOKProtest = 0;
                foreach ($logEvos as $logEvo) {
                    if (isset($logEvo->getData()["status"])) {
                        if ($logEvo->getData()["status"]["id"] == 3) {

                            $countProtest += 1;

                            $strCreatedDate = strtotime(date_format($evaluation->getCreatedAt(), "Y-m-d H:i:s"));
                            $strUpdatedDate = strtotime(date_format($evaluation->getUpdatedAt(), "Y-m-d H:i:s"));
                            $strDiff = $strUpdatedDate - $strCreatedDate;
                            $avgDateTime = $strDiff / $countProtest;
                            $avgDateTimeToDate = date("d H:i:s", $avgDateTime);

                            $arrDiffMax[] = $strDiff;
                            $twoDays = 0;
                            $arrMax = max($arrDiffMax);
                            $maxDate = date("d H:i:s", $arrMax);
                            foreach ($arrDiffMax as $item) {
                                if ($item > 172800) {
                                    $twoDays += 1;
                                }
                            }
                            $logEvoRes [] = [
//                                "teamLeader"=>$evaluation->,
                                "userName" => $evaluation->getUser()->getTeamId(),
                                "evaID" => $logEvo->getObjectId(),
                                "status" => $logEvo->getData()["status"]["id"],
                                "createdDate" => date_format($evaluation->getCreatedAt(), "Y-m-d H:i:s"),
                                "updatedDate" => date_format($evaluation->getUpdatedAt(), "Y-m-d H:i:s"),
                                "twoDays" => $twoDays,
                                "maxTime" => $arrMax,
                                "maxTimetoDate" => $maxDate,
                                "countProtest" => $countProtest,
                                "avgDateTimeToDate" => $avgDateTimeToDate,
                                "countOKProtest" => $countOKProtest,

                            ];
                            $varObjectId = $logEvo->getObjectId();
                            $varItiraz = true;
                        } else {
                            if ($varObjectId == $logEvo->getObjectId() && $varItiraz == true) {
                                if ($logEvo->getData()["status"]["id"] == 5) {
                                    $countOKProtest += 1;
                                    $strCreatedDate = strtotime(date_format($evaluation->getCreatedAt(), "Y-m-d H:i:s"));
                                    $strUpdatedDate = strtotime(date_format($evaluation->getUpdatedAt(), "Y-m-d H:i:s"));
                                    $strDiff = $strUpdatedDate - $strCreatedDate;
                                    $avgDateTime = $strDiff / $countProtest;
                                    $avgDateTimeToDate = date("d H:i:s", $avgDateTime);

                                    $arrDiffMax[] = $strDiff;

                                    $twoDays = 0;
                                    $arrMax = max($arrDiffMax);
                                    $maxDate = date("d H:i:s", $arrMax);
                                    foreach ($arrDiffMax as $item) {
                                        if ($item > 172800) {
                                            $twoDays += 1;
                                        }
                                    }


                                    $logEvoRes [] = [
                                        "userName" => $evaluation->getUser()->getTeamId(),
                                        "evaID" => $logEvo->getObjectId(),
                                        "status" => $logEvo->getData()["status"]["id"],
                                        "createdDate" => date_format($evaluation->getCreatedAt(), "Y-m-d H:i:s"),
                                        "updatedDate" => date_format($evaluation->getUpdatedAt(), "Y-m-d H:i:s"),
                                        "twoDays" => $twoDays,
                                        "maxTime" => $arrMax,
                                        "maxTimeToDate" => $maxDate,
                                        "countProtest" => $countProtest,
                                        "avgDateTimeToDate" => $avgDateTimeToDate,
                                        "countOKProtest" => $countOKProtest,
                                        "countRedProtest" => $countProtest - $countOKProtest,

                                    ];


                                    $varObjectId = 0;
                                    $varItiraz = false;
                                }
                            } else {
                                $varObjectId = 0;
                                $varItiraz = false;
                            }
                        }

                    }
                }
            }
        }
//        dump($logEvoRes);
//        exit();
//        return new JsonResponse($logEvoRes);
        return $this->render("team_leader_eva_protest_count/index.html.twig", [
            "datatable" => $logEvoRes,
            "form" => $form->createView()
        ]);

    }
}