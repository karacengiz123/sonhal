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
use App\Entity\Team;
use App\Entity\User;
use App\Entity\UserProfile;
use App\Helpers\Date;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class teamLeaderEvaTotalCount extends AbstractController
{


    /**
     * @Route("/teamLeaderEvaTotalCount" ,name="team-leader-eva-total-count")
     * @param Request $request
     * @return Response
     */
    public function teamLeaderEvaTotalCount(Request $request)
    {
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
                    "multiple"=>"multiple"]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Listele'
            ])
            ->getForm();

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $formHandle = $form->getViewData();
            $teamLeaderId = $formHandle["team"];
            $firsDate = $formHandle["firstDate"]->format("Y-m-d 00:00:00");
            $lastDate = $formHandle["lastDate"]->format("Y-m-d 23:59:59");

            $team = $em->find(Team::class,$teamLeaderId);
            $users = $team->getUsers()->toArray();
            $userId=[];
            /**
             * @var User $user
             */
            foreach ($users as $user){
                $userId []= $user->getId();
            }
            $evaluations=$this->getDoctrine()->getRepository(Evaluation::class);

            $evaluatedList=$evaluations->createQueryBuilder('e');
            $evaluatedList
                ->select("e as evaItem,
                COUNT(e.id) as count,
                AVG(e.score) as AVGScore,
                    AVG(e.duration) as AVGDuration,
                    MIN(e.score) as MINScore,
                    MAX(e.score) as MAXScore"
                )
                ->where("e.createdAt BETWEEN :sDate AND :eDate")
                ->setParameter("sDate",$firsDate)
                ->setParameter("eDate",$lastDate)
                ->andWhere("e.evaluative=:evaluative")
                ->setParameter("evaluative",$team->getManager()->getId())
                ->groupBy('e.evaluative');
            $evaluatedList = $evaluatedList->getQuery()->getResult();
//dump($evaluatedList);
//exit();
            return $this->render("team_leader_eva_total_count/teamLeaderEvaTotalCount.html.twig", [
                "datatable" => $evaluatedList,
                "form" => $form->createView()
            ]);
        }
        return $this->render("team_leader_eva_total_count/teamLeaderEvaTotalCount.html.twig", [
            "datatable" => "",
            "form" => $form->createView()
        ]);
    }
}