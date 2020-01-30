<?php

namespace App\Controller;

use koolreport\morris_chart\Area;
use koolreport\widgets\google\AreaChart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class KoolReportController extends AbstractController
{
    /**
     * @Route("/kool/report", name="kool_report")

     */
    public function myreport()
    {
        $time_sale = array(
            array("month"=>"January","sale"=>32000,"cost"=>40000),
            array("month"=>"February","sale"=>48000,"cost"=>39000),
            array("month"=>"March","sale"=>35000,"cost"=>38000),
            array("month"=>"April","sale"=>40000,"cost"=>37000),
            array("month"=>"May","sale"=>60000,"cost"=>45000),
            array("month"=>"June","sale"=>73000,"cost"=>47000),
            array("month"=>"July","sale"=>80000,"cost"=>60000),
            array("month"=>"August","sale"=>78000,"cost"=>65000),
            array("month"=>"September","sale"=>60000,"cost"=>45000),
            array("month"=>"October","sale"=>83000,"cost"=>71000),
            array("month"=>"November","sale"=>45000,"cost"=>40000),
            array("month"=>"December","sale"=>39000,"cost"=>60000),
        );

        $aaa = AreaChart::create(array(
            "title"=>"Sale vs Cost",
            "dataSource"=>$time_sale,
            "columns"=>array(
                "month",
                "sale"=>array(
                    "label"=>"Sale",
                    "type"=>"number",
                    "prefix"=>"$"
                ),
                "cost"=>array(
                    "label"=>"Cost",
                    "type"=>"number",
                    "prefix"=>"$"
                ),
            ),
        ));

//        dump($aaa);
//        exit();

        return $this->render('kool_report/index.html.twig', [
            'aaa'=>$aaa
        ]);
    }
}
