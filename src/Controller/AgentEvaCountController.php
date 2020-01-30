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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AgentEvaCountController extends AbstractController
{
//    /**
//     * @IsGranted("ROLE_CALL_DETAIL")
//     * @Route("/team-leader-eva-count", name="team-leader-eva-count")
//     * @param Request $request
//     * @return Response
//     */
//    public function form(Request $request)
//    {
//        $form = $this->createFormBuilder()
//            ->add("firstDate", DateType::class, [
//                "label" => "İlk Tarih",
//                "attr" => ["class" => "form-control"],
//                'widget' => 'single_text'
//            ])
//            ->add("lastDate", DateType::class, [
//                "label" => "Son Tarih",
//                "attr" => ["class" => "form-control"],
//                'widget' => 'single_text'
//            ])
//            ->add("time", ChoiceType::class, [
////                "label" => "Saat Aralıkları",
//                "label" => " ",
//                'choices' => [
//                    "1 Günlük Aralıklarla" => 24 * 60 * 60,
////                    "15 Dakikalık Aralıklarla" => 15 * 60,
////                    "30 Dakikalık Aralıklarla" => 30 * 60,
////                    "1 Saatlik Aralıklarla" => 60 * 60,
////                    "1 Aylık Aralıklarla" => 30 * 24 * 60 * 60,
//                ],
//                "attr" => ["class" => "form-control",
//                    "hidden" => true
//                ]
//            ])
//            ->getForm();
//        return $this->render('team_leader_eva_count/selectCategory.html.twig', [
//            "form" => $form->createView(),]);
//    }
//
//    /**
//     * @Route("/team-leader-eva-counts")
//     * @param Request $request
//     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
//     */
//    public function igdas(Request $request)
//    {
//        $formValidate = $request->request->all();
//        $firstTime = date('Y-m-d H:i:s', strtotime($formValidate["form"]["firstDate"] . "00:00:00"));
//
//        $lastTime = date('Y-m-d H:i:s', strtotime($formValidate["form"]["lastDate"] . "23:59:59"));
//        $time = $formValidate["form"]["time"];
//
//        if ($formValidate["form"]["firstDate"] == "" && $formValidate["form"]["lastDate"] == "") {
//            return new Response("Lütfen bir tarih aralığı seçiniz..");
//        } else {
//            $arr = [];
//            if ($formValidate["form"]["firstDate"] == $formValidate["form"]["lastDate"] && $formValidate["form"]["time"] == 86400) {
//                $rows = $this->teamLeaderEvaCount($firstTime, $lastTime);
//                if (count($rows) > 0) {
//                    foreach ($rows as $row) {
//                        $row["dateRange"] = date('Y-m-d', strtotime($formValidate["form"]["firstDate"] . "00:00:00"));
//                        $row["dateRangeTime"] = date('H:i:s', strtotime($formValidate["form"]["firstDate"] . "00:00:00")) . " - " . date('H:i:s', strtotime($formValidate["form"]["lastDate"] . "23:59:59"));
//
//                        $arr[] = $row;
//                    }
//                }
//            } else {
//                $lastTime = date("Y-m-d H:i:s", strtotime($lastTime) + 86400);
//
//                $ranges = range(
//                    strtotime($firstTime),
//                    strtotime($lastTime),
//                    $time
//                );
//
//
//                $prevDate = null;
//                $arr = [];
//                foreach ($ranges as $range) {
//                    if ($prevDate == null) {
//                        $prevDate = $range;
//                        continue;
//                    }
//                    $rows = $this->teamLeaderEvaCount(date('Y-m-d H:i:s', $prevDate), date('Y-m-d H:i:s', $range - 1));
//                    if (count($rows) > 0) {
//                        foreach ($rows as $row) {
//                            $row["dateRange"] = date('Y-m-d', $prevDate);
//                            $row["dateRangeTime"] = date('H:i:s', $prevDate) . " - " . date('H:i:s', $range - 1);
//                            $arr[] = $row;
//                        }
//                    }
//                    $prevDate = $range;
//                }
//            }
//            return $this->json($arr);
//        }
//
//    }


    /**
     * @Route("/agentEvaCount" ,name="agent-eva-count")
     * @param Request $request
     */
    public function agentEvaCount(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

//        $repo = $em->getRepository(LogEvaluation::class)->findAll();
//
//        dump($repo);
//
//        exit();
//
//


//        $form = $this->createFormBuilder()
//            ->add("firstDate", DateType::class, [
//                "label" => "İlk Tarih",
//                "attr" => ["class" => "form-control"],
//                'widget' => 'single_text'
//            ])
//            ->add("lastDate", DateType::class, [
//                "label" => "Son Tarih",
//                "attr" => ["class" => "form-control"],
//                'widget' => 'single_text'
//            ])
//            ->add('save', SubmitType::class, [
//                'label' => 'Listele'
//            ])
//            ->getForm();
//
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//
//            $formHandle = $form->getViewData();
////            $teamLeaderId = $formHandle["team"];
//            $firsDate = $formHandle["firstDate"]->format("Y-m-d 00:00:00");
//            $lastDate = $formHandle["lastDate"]->format("Y-m-d 23:59:59");

        $users = $em->getRepository(User::class)->findAll();
//            $users = $team->getUsers()->toArray();
        $userId = [];
        /**
         * @var User $user
         */
        foreach ($users as $user) {
            $userId [] = $user->getId();
        }
        $evaluations = $this->getDoctrine()->getRepository(Evaluation::class);

        $evaluatedList = $evaluations->createQueryBuilder('e');
        $evaluatedList
//            ->select("(e.updatedAt) as updatedAt,(e.createdAt) as createdAt,e as evaItem,COUNT(e.id) as count")
            ->select("e.updatedAt as maxupdatedAt,e.createdAt as maxcreatedAt,SUM(e.updatedAt) as updatedAt,SUM(e.createdAt) as createdAt,e as evaItem,COUNT(e.id) as count")
//                ->where("e.createdAt BETWEEN :sDate AND :eDate")
//                ->setParameter("sDate",$firsDate)
//                ->setParameter("eDate",$lastDate)
            ->andWhere("e.status =:status")
//                ->andWhere("e.evaluative=:evaluative")
//                ->setParameter("evaluative",$userId)
            ->setParameter("status", 3)
//                ->andWhere(
//                    $evaluatedList->expr()->in("e.user",$userId)
//                )
            ->groupBy('e.user');
        $evaluatedList = $evaluatedList->getQuery()->getResult();
        $row = [];


        $maxrow = [];

        foreach ($evaluatedList as $evaList) {


            /**
             * @var User $user
             * @var Evaluation $evaListItem
             */
            $evaListItem = $evaList["evaItem"];
            $user = $evaListItem->getUser();
            $max1 = date_format($evaList["maxupdatedAt"], 'd-m-Y H:i:s');
            $max1str = strtotime($max1);
            $max2 = date_format($evaList["maxcreatedAt"], 'd-m-Y H:i:s');
            $max2str = strtotime($max2);
            $diffmax = $max1str - $max2str;
            $maxxx[] = $diffmax;
            $twoDays = 0;

            foreach ($maxxx as $maxx) {
                if ($maxx > 172800) {
                    $twoDays += 1;

                }
            }


//            $max2str= ($max1-$max2)/ $evaList["count"];
            $row[] = [
                "Kalite" => $user->getUserProfile()->__toString(),
                "evaCount" => $evaList["count"],
                "AVGTime" => ($evaList["updatedAt"] - $evaList["createdAt"]) / $evaList["count"],
                "maxupdated" => $evaList["maxupdatedAt"],
                "maxcreated" => $evaList["maxcreatedAt"],
                "maxTime" => max($maxxx),
                "twoDays" => $twoDays,
            ];
        }

        dump($row);
        exit();


    }
}