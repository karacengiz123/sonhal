<?php


namespace App\Controller;


use App\Entity\Evaluation;
use App\Entity\EvaluationAnswer;
use App\Entity\FormCategory;
use App\Entity\FormQuestion;
use App\Entity\FormQuestionOption;
use App\Entity\FormTemplate;
use App\Entity\Team;
use Doctrine\DBAL\Driver\AbstractDB2Driver;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TeamLeaderQuestionController extends AbstractController
{


    /**
     * @IsGranted("ROLE_TEAMLEADERQUESTION")
     * @Route("/team_leader_question",name="team-leader-question")
     * @param Request $request
     * @return Response
     */
    public function teamLeaderQuestion(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $teams = $em->getRepository(Team::class)->findAll();
        $choiceTeam = [];
        foreach ($teams as $team){
            $choiceTeam [$team->getName()] = $team->getManager()->getId();
        }

        $form = $this->createFormBuilder()
            ->add("firstDate", DateType::class, [
                "label" => "İlk Tarih",
                "attr" => ["class" => "form-control"],
                'widget' => 'single_text',
//                'html5' => false,

            ])
            ->add("lastDate", DateType::class, [
                "label" => "Son Tarih",
                "attr" => ["class" => "form-control"],
                'widget' => 'single_text',
//                'html5' => false,

            ])
            ->add("team", ChoiceType::class, [
                "label" => "Takım Seçiniz",
                'choices' => $choiceTeam,
                "attr" => ["class" => "form-control"]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Listele'
            ])
            ->getForm();

        $form->handleRequest($request);

        $resultRowColumns = [];
        $result = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $formHandle = $form->getViewData();
            $teamLeaderId = $formHandle["team"];
            $firsDate = $formHandle["firstDate"]->format("Y-m-d 00:00:00");
            $lastDate = $formHandle["lastDate"]->format("Y-m-d 23:59:59");

            $rows = [];
            $evaluations = $em->getRepository(Evaluation::class)->createQueryBuilder("e")
                ->where("e.createdAt BETWEEN :sDate AND :eDate")
                ->andWhere("e.evaluative=:evaluative")
                ->setParameters([
                    "sDate"=>$firsDate,
                    "eDate"=>$lastDate,
                    "evaluative"=>$teamLeaderId
                ])->getQuery()->getResult();

            if (count($evaluations) > 0) {
                foreach ($evaluations as $evaluation) {
                    $userName = $evaluation->getUser()->getUserProfile()->getFirstName() . " " . $evaluation->getUser()->getUserProfile()->getLastName();
                    $formTemlate = $evaluation->getFormTemplate()->getTitle();
                    foreach ($evaluation->getEvaluationAnswers()->toArray() as $answer) {
                        $rows [$userName][$formTemlate][$answer->getQuestion()->getTitle()][] = [
                            "score" => $answer->getAnswer()->getScore()
                        ];
                    }
                }
                foreach ($rows as $rowKey => $rowVal) {

                    foreach ($rowVal as $valKey => $val) {
                        $count = 0;
                        $scoreTotal = 0;
                        $maxScoreTotal = 0;
                        foreach ($val as $itemKey => $itemVal) {
                            $count = count($itemVal);
                            $sumColumScore = 0;
                            $maxColumScore = 0;
                            foreach ($itemVal as $Key => $Value) {
                                $sumColumScore += $Value["score"];
                                $scoreTotal += $Value["score"];
                                if ($Value["score"] > $maxColumScore) {
                                    $maxColumScore = $Value["score"];
                                    $maxScoreTotal += $Value["score"];
                                }
                                $resultRowColumns [$rowKey][$valKey][$itemKey] = [
                                    "score" => $sumColumScore,
                                    "maxScore" => $maxColumScore,
                                    "count" => $count,
                                ];
                            }
                        }
                        $resultRowColumns [$rowKey][$valKey]["TOPLAM"] = [
                            "score" => $scoreTotal,
                            "maxScore" => $maxScoreTotal,
                            "count" => $count,
                        ];
                    }
                }
            }




            foreach ($resultRowColumns as $nameKey => $nameVal) {
                foreach ($nameVal as $formKey => $formVal) {
                    foreach ($formVal as $questionKey => $questionVal) {
                        $result [$nameKey][$formKey][$questionVal["count"]][] = [
                            "question" => $questionKey,
                            "score" => $questionVal["score"],
                            "maxScore" => $questionVal["maxScore"]
                        ];

                    }
                }
            }



        }

        return $this->render("team_leader_question/selectQuestion.html.twig", [
            "datatable" => $result,
            "form"=>$form->createView()
        ]);

    }

}