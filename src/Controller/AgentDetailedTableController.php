<?php
/**
 * Created by PhpStorm.
 * User: cengi
 * Date: 26.04.2019
 * Time: 17:13
 */

namespace App\Controller;


use App\Entity\AcwLog;
use App\Entity\AcwType;
use App\Entity\AgentBreak;
use App\Entity\BreakType;
use App\Entity\Calls;
use App\Entity\HoldLog;
use App\Entity\LoginLog;
use App\Entity\StateLog;
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

class AgentDetailedTableController extends AbstractController
{
    /**
     * @Route("/agent-detailed-table", name="agent_detailed_table_report")
     * @param Request $request
     * @return Response
     */
    public function agentDetailedTable(Request $request)
    {
        $rowData = [];
        $em = $this->getDoctrine()->getManager();
        $form = $this->createFormBuilder()
            ->add("startDate", DateType::class, [
                "label" => "İlk Tarih",
                "attr" => ["class" => "form-control"],
                'widget' => 'single_text',
//                'html5' => false,

            ])
            ->add("endDate", DateType::class, [
                "label" => "Son Tarih",
                "attr" => ["class" => "form-control"],
                'widget' => 'single_text',
//                'html5' => false,

            ])
            ->add('save', SubmitType::class, [
                'label' => 'Listele'
            ])
            ->getForm();

        $form->handleRequest($request);

//        $startDate = "'2019-10-10 00:00:00'";
//        $endDate = "'2019-10-10 23:59:59'";
        $resultRowColumns = [];
        $result = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $formHandle = $form->getViewData();
            $startDate = $formHandle["startDate"]->format("Y-m-d 00:00:00");
            $endDate = $formHandle["endDate"]->format("Y-m-d 23:59:59");
            $userProfiles = $this->getDoctrine()->getRepository(UserProfile::class)->createQueryBuilder('up')->leftJoin('up.user', 'u')
                ->select('u.id as user_id, up.firstName, up.lastName, up.tckn')->getQuery()->getArrayResult();
            $userProfiles = array_column($userProfiles, null, 'user_id');


            $data = $this->agentDetailedTableOneDay($startDate, $endDate);
//            dump($data);
//            exit();
            $oneDayData = array_column($data, null, 'user_id');
            foreach ($userProfiles as $userProfile) {

                $rowData[$userProfile['user_id']] = [
                    "dateRangeTime" => $startDate ,
                    "dateRangeTime2" => "00:00:00 - 23:59:59",
                    "TC" => $userProfile['tckn'],
                    "Personel" => $userProfile['firstName'] . " " . $userProfile['lastName'],

                    "YEMEK" => gmdate("H:i:s",$oneDayData[$userProfile['user_id']]['bt1']) ?? '00:00:00',
                    "IHTIYAC" => gmdate("H:i:s",$oneDayData[$userProfile['user_id']]['bt4']) ?? '00:00:00',
                    "ToplamMolaSuresi" => gmdate("H:i:s",$oneDayData[$userProfile['user_id']]['breakTotals'] )?? '00:00:00',

                    "stateOneTotal" =>gmdate("H:i:s", $oneDayData[$userProfile['user_id']]['stateOneTotal']) ?? '00:00:00',
                    "ACW" =>gmdate("H:i:s", $oneDayData[$userProfile['user_id']]['acw1']) ?? '00:00:00',
                    "SORU" => gmdate("H:i:s",$oneDayData[$userProfile['user_id']]['acw2']) ?? '00:00:00',
                    "DisArama" => gmdate("H:i:s",$oneDayData[$userProfile['user_id']]['acw3']) ?? '00:00:00',
                    "TOPLANTI" => gmdate("H:i:s",$oneDayData[$userProfile['user_id']]['acw4']) ?? '00:00:00',
                    "SINAV" => gmdate("H:i:s",$oneDayData[$userProfile['user_id']]['acw5']) ?? '00:00:00',
                    "EGITIM" => gmdate("H:i:s",$oneDayData[$userProfile['user_id']]['acw6']) ?? '00:00:00',
                    "GeriBildirim" => gmdate("H:i:s",$oneDayData[$userProfile['user_id']]['acw7']) ?? '00:00:00',
                    "ToplamIslemSuresi" => gmdate("H:i:s",$oneDayData[$userProfile['user_id']]['acwTotals']) ?? '00:00:00',

//                    "logMIN" => strtotime($oneDayData[$userProfile['user_id']]['logMIN']),
//                    "logMAX" => strtotime($oneDayData[$userProfile['user_id']]['logMAX']),

                    "LoginFirstLoginLast" => gmdate("H:i:s",strtotime($oneDayData[$userProfile['user_id']]['logMAX']) - strtotime($oneDayData[$userProfile['user_id']]['logMIN'] ))??'00:00:00',

                    "ToplamLoginSuresi" => gmdate("H:i:s",$oneDayData[$userProfile['user_id']]['logTotal'])?? '00:00:00',

                    "ToplamGelenCagriSayisi" => $oneDayData[$userProfile['user_id']]['CallsTotalCount'] ?? '0',
                    "ToplamKonusmaSuresi" => gmdate('H:i:s', $oneDayData[$userProfile['user_id']]['CallsTotalDuration']) ?? '00:00:00',

                    "Hold" => gmdate('H:i:s', $oneDayData[$userProfile['user_id']]['holdTotal']) ?? '00:00:00',
                    'acht' =>gmdate("H:i:s",$oneDayData[$userProfile['user_id']]['CallsTotalDuration']?($oneDayData[$userProfile['user_id']]['CallsTotalDuration'] + $oneDayData[$userProfile['user_id']]['acwTotals'])/$oneDayData[$userProfile['user_id']]['CallsTotalCount'] :0)??'00:00:00'
                ];
            }

//            dump($rowData);
//            exit;
        }
//return $rowData;
        return $this->render("agentDetailedTable/agentDetailedTable.html.twig", [
            "datatable" => $rowData,
            "form" => $form->createView()
        ]);
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return mixed
     */
    public function agentDetailedTableOneDay($startDate, $endDate)
    {
//        $startDate =   "'2019-10-10 00:00:00'";
//        $endDate = "'2019-10-10 23:59:59'";
        $breakTypes = $this->getDoctrine()->getRepository(BreakType::class)->findAll();
        $acwTypes = $this->getDoctrine()->getRepository(AcwType::class)->findAll();
//        $registerLogs   = $this->getDoctrine()->getRepository(RegisterLog::class)->findAll();
        $totalBreak = 0;
        foreach ($breakTypes as $breakType) {
            $breakTYpeQueries[] = "(select sum(ab_{$breakType->getId()}.duration) from " . AgentBreak::class . "  ab_{$breakType->getId()} where ab_{$breakType->getId()}.startTime between '" . $startDate . "' and '" . $endDate . "' and  ab_{$breakType->getId()}.user = u.id and  ab_{$breakType->getId()}.breakType=" . $breakType->getId() . ")as bt" . $breakType->getId();
            $totalBreak="(select sum(breakTotal.duration) from " . AgentBreak::class . " breakTotal where breakTotal.endTime between '" . $startDate . "' and '" . $endDate . "' and breakTotal.user = u.id ) as breakTotals";
        }

        $btQuery = implode(", ", $breakTYpeQueries);

        foreach ($acwTypes as $acwType) {
            $acwTypeQueries[] = "(select sum(acw_{$acwType->getId()}.duration) from " . AcwLog::class . " acw_{$acwType->getId()} where acw_{$acwType->getId()}.startTime between '" . $startDate . "' and '" . $endDate . "' and acw_{$acwType->getId()}.user = u.id and  acw_{$acwType->getId()}.acwType=" . $acwType->getId() . ")as acw" . $acwType->getId();
            $totalAcw="(select sum(totalAcw.duration) from " . AcwLog::class . " totalAcw where totalAcw.endTime between '" . $startDate . "' and '" . $endDate . "' and totalAcw.user = u.id ) as acwTotals";

        }
        $acwQuery = implode(", ", $acwTypeQueries);




//        $registerQuery = "(select sum(reg.duration) from " . RegisterLog::class . " reg where reg.StartTime between '" . $startDate . "' and '" . $endDate . "' and reg.user = u.id) as regTotal";
        $readyQuery = "(select sum(st.duration) from " . StateLog::class . " st where st.startTime between '" . $startDate . "' and '" . $endDate . "' and st.user = u.id  and st.state = 1) as stateOneTotal";
        $loginQuery = "(select sum(log.duration) from " . LoginLog::class . " log  where log.StartTime between '" . $startDate . "' and '" . $endDate . "' and log.userId = u.id) as logTotal";
        $holdQuery = "(select sum(hold.duration) from " . HoldLog::class . " hold where hold.endTime between '" . $startDate . "' and '" . $endDate . "' and hold.user = u.id and hold.callType = 'InBound') as holdTotal";


        $loginMin = "(select MIN(logmin.StartTime) from " . LoginLog::class . " logmin where logmin.StartTime  between '" . $startDate . "' and '" . $endDate . "' and logmin.userId = u.id) as logMIN";
        $loginMAX = "(select MAX(logmax.EndTime) from " . LoginLog::class . " logmax where logmax.StartTime  between '" . $startDate . "' and '" . $endDate . "' and logmax.userId = u.id) as logMAX";
        $callsTotalCount = "(select count(cc.idx) as totalCall  from " . Calls::class . " cc
                     where cc.callType = 'Inbound' and cc.callStatus = 'Done'
              and cc.dt between '" . $startDate . "' and '" . $endDate . "'
              and cc.user = u.id) as CallsTotalCount";
        $callsTotalDuration = "(select sum(c.durExten) as totalCallDuration from " . Calls::class . " c
                     where c.callType = 'Inbound' and c.callStatus = 'Done'
              and c.dt between '" . $startDate . "' and '" . $endDate . "'
              and c.user = u.id) as CallsTotalDuration";


        $query = $this->getDoctrine()->getManager()->createQuery('select u.id as user_id , 
          ' . $btQuery . ',
          ' . $totalBreak . ',
          ' . $totalAcw . ',  
          ' . $acwQuery . ' ,
          ' . $loginQuery . ' ,
          ' . $holdQuery . ' ,
          ' . $loginMin . ' ,
          ' . $loginMAX . ',
          ' . $callsTotalCount . ',
          ' . $callsTotalDuration . ' ,
          ' . $readyQuery . ' 
          from ' . User::class . '
         as u   group by user_id');


        return $query->getArrayResult();
//             where c.callType = 'Inbound' and c.callStatus = 'Done'


    }


}