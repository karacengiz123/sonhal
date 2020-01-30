<?php
/**
 * Created by PhpStorm.
 * User: cengi
 * Date: 2.05.2019
 * Time: 17:25
 */

namespace App\Controller;


use App\Asterisk\Entity\Cdr;
use App\Asterisk\Entity\IvrLogs;
use App\Entity\User;
use App\Entity\UserProfile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CallDetail extends AbstractController
{
    /**
     * @IsGranted("ROLE_REPORTER")
     * @Route("/call", name="call_detail")
     * @param Request $request
     * @return Response
     */
    public function form(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add("firstDate", DateType::class, [
                "label" => "İlk Tarih",
                "attr" => ["class" => "form-contcarol"],
                'widget' => 'single_text'
            ])
            ->add("lastDate", DateType::class, [
                "label" => "Son Tarih",
                "attr" => ["class" => "form-control"],
                'widget' => 'single_text'
            ])
            ->add("time", ChoiceType::class, [
                "label" => "Saat Aralıkları",
                'choices' => [
                    "15 Dakika Aralıklarla" => 15 * 60,
                    "30 Dakika Aralıklarla" => 30 * 60,
                    "1 Saat Aralıklarla" => 60 * 60,
                    "24 Saat Aralıklarla" => 24 * 60 * 60,
                    "30 Günlük Aralıklarla" => 30 * 24 * 60 * 60,
                ],
                "attr" => ["class" => "form-control"]

            ])
            ->getForm();
        return $this->render('call_detail/index.html.twig', [
            "form" => $form->createView(),]);
    }

    /**
     * @IsGranted("ROLE_REPORTER")
     * @Route("/calldetails")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function igdas(Request $request)
    {
        $formValidate = $request->request->all();
        $firstTime = date('Y-m-d H:i:s', strtotime($formValidate["form"]["firstDate"] . "00:00:00"));
        $lastTime = date('Y-m-d H:i:s', strtotime($formValidate["form"]["lastDate"] . "23:59:59"));
        $time = $formValidate["form"]["time"];

//        return $this->json(date('Y-m-d H:i:s',1555020000));
        if ($formValidate["form"]["firstDate"] == "" && $formValidate["form"]["lastDate"] == "") {
            return new Response("Lütfen bir tarih aralığı seçiniz..");
        } else {
            $ranges = range(
                strtotime($firstTime),
                strtotime($lastTime),
                $time
            );


            $prevDate = null;
            $arr = [];
            foreach ($ranges as $range) {
                if ($prevDate == null) {
                    $prevDate = $range;
                    continue;
                }
                $rows = $this->callDetail()(date('Y-m-d H:i:s', $prevDate),
                    date('Y-m-d H:i:s', $range - 1));
                if (count($rows) > 0) {
                    foreach ($rows as $row) {
                        $row["dateRange"] = date('Y-m-d', $range) . " " .
                            date('H:i:s', $prevDate) . " - " .
                            date('H:i:s', $range);
                        $arr[] = $row;
                    }
                }
                $prevDate = $range;
            }
            return $this->json($arr);
        }

    }
//@Route("/callDetail")
    /**
     * @IsGranted("ROLE_REPORTER")
     * @return array
     */
    public function callDetail()
    {

        $em = $this->getDoctrine()->getManager();
        $callDetails = $em->getRepository(IvrLogs::class)
            ->createQueryBuilder('u')
            ->select('u.dt,u.callId')
            ->getQuery()->getResult();

        $rec = [];
        foreach ($callDetails as $callDetail) {
            $callDetail = $em->getRepository(Cdr::class)
                ->createQueryBuilder('c')
                ->where('c.callId=:callId')
                ->setParameter('callId', $callDetail['callId'])
                ->getQuery()->getArrayResult();
            if (count($callDetail) > 0) {
                $rec[] = $callDetail;
            }
            foreach ($rec as $item) {
                $duration = 0;
                $src = "";
                $billsec = 0;
                foreach ($item as $value) {

                    $duration=$duration+$value["duration"];

                    $billsec = $billsec + $value["billsec"];
                    if ($src == "") {
                        $src = $value["src"];
                    }
                    if ($value["disposition"] == "ANSWERED") {
                        if (strlen($value["dst"]) == 10) {
                            $usp = $em->getRepository(UserProfile::class)
                                ->createQueryBuilder('up')
                                ->select('up.firstName,up.lastName')
                                ->where('up.extension=:extension')
                                ->setParameter('extension', $value['dst'])
                                ->getQuery()->getArrayResult();
                            if (count($usp) > 0) {
                                $karsilayan = $usp[0]['firstName'] . " " . $usp[0]['lastName'];
                            } else {
                                $karsilayan = " ";
                            }
                        } else {
                            $karsilayan = $value['dst'];
                        }
                    }

                    $baslangicZamani = date_format($value["calldate"], 'd-m-Y H:i:s');
                    $convert = strtotime($baslangicZamani);
                    $convertBitisZamani = $convert + $duration;
                    $bitisZamani = date('d-m-Y H:i:s', $convertBitisZamani);

                }

                $row[]=[
                    "arayannumara" => $src,
                    "aranannumara" => $karsilayan,
                    "konusmasuresi" => $billsec,
                    "ivrsuresi" => $duration,
                    "baslangicZamani" => $baslangicZamani,
                    "bitisZamani" => $bitisZamani,
                ];
            }
        }
        return $row;

//        return $this->json($row);
//        dump($row);
//        exit();
    }
}