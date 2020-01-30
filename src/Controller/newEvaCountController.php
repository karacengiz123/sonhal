<?php


namespace App\Controller;


use App\Asterisk\Entity\Queues;
use App\Entity\DateTrait;
use App\Entity\Evaluation;
use App\Entity\FormCategory;
use App\Entity\FormQuestion;
use App\Entity\FormQuestionOption;
use App\Entity\FormTemplate;
use App\Entity\Team;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class newEvaCountController extends AbstractController
{

    /**
     * @IsGranted("ROLE_TEAMLEADERQUESTION")
     * @Route("/new-eva-count",name="new_eva_count")
     * @param Request $request
     * @return Response
     */
    public function teamLeaderCategory(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $teams = $em->getRepository(Team::class)->findAll();
        foreach ($teams as $team) {
            $choiceTeam [$team->getName()] = $team->getManager()->getId();
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
                "attr" => ["class" => "form-control"]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Listele'
            ])
            ->getForm();

        $form->handleRequest($request);
        $rowColumns = [];

        $resultRowColumns = [];
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
                    "sDate" => $firsDate,
                    "eDate" => $lastDate,
                    "evaluative" => $teamLeaderId
                ])->getQuery()->getResult();

            if (count($evaluations) > 0) {
                foreach ($evaluations as $evaluation) {
                    $userName = $evaluation->getUser()->getUserProfile()->getFirstName() . " " . $evaluation->getUser()->getUserProfile()->getLastName();
                    $formCatagories = $em->getRepository(FormCategory::class)->findBy(["formTemplate" => $evaluation->getFormTemplate()]);
                    if (count($formCatagories) > 0) {
                        $rows [$userName][$evaluation->getFormTemplate()->getTitle()]["title"] = $formCatagories;
                    }
                }
            }
            foreach ($rows as $rowKey => $rowValue) {
                foreach ($rowValue as $valKey => $val) {
                    foreach ($val as $item) {
                        foreach ($item as $aaa) {
                            $rowColumns [$rowKey][$valKey][$aaa->getTitle()]["sort"][] = $aaa->getSort();
                            $rowColumns [$rowKey][$valKey][$aaa->getTitle()]["maxScore"][] = $aaa->getMaxScore();
                        }
                    }
                }
            }
            foreach ($rowColumns as $rowKey => $rowValue) {
                foreach ($rowValue as $valKey => $val) {
                    $totalSort = 0;
                    $totalMaxScore = 0;
                    foreach ($val as $itemKey => $itemValue) {
                        $resultRowColumns [$rowKey][$valKey][$itemKey]["sort"] = [
                            "count" => count($itemValue["sort"]),
                            "sumSort" => array_sum($itemValue["sort"])
                        ];
                        $totalSort += array_sum($itemValue["sort"]);


                        $resultRowColumns [$rowKey][$valKey][$itemKey]["maxScore"] = [
                            "count" => count($itemValue["maxScore"]),
                            "sumMaxScore" => array_sum($itemValue["maxScore"])
                        ];
                        $totalMaxScore += array_sum($itemValue["maxScore"]);

                    }

                    $resultRowColumns [$rowKey][$valKey]["total"]["sort"] = [
                        "count" => count($itemValue["sort"]),
                        "sumSort" => $totalSort
                    ];

                    $resultRowColumns [$rowKey][$valKey]["total"]["maxScore"] = [
                        "count" => count($itemValue["maxScore"]),
                        "sumMaxScore" => $totalMaxScore
                    ];
                }
            }
        }
//        dump($resultRowColumns);
//        exit();
        $awawResult = [];

        foreach ($resultRowColumns as $asdKey => $asdVal) {
            foreach ($asdVal as $adsKey => $adsVal) {
                foreach ($adsVal as $dsaKey => $dsaVal) {
                    $awawResult [$asdKey][$adsKey][$dsaVal["sort"]["count"]][] = [
                        "question" => $dsaKey,
                        "sort" => $dsaVal["sort"]["sumSort"],
                        "maxScore" => $dsaVal["maxScore"]["sumMaxScore"]
                    ];

                }
            }
        }
//        dump($awawResult);
//        exit();
        return $this->render("newEvaCount/teamLeaderEvaTotalCount.html.twig", [
            "datatable" => $awawResult,
            "form" => $form->createView()
        ]);

    }
}