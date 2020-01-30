<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MonitoringController extends AbstractController
{
    /**
     * @Route("/monitoring", name="monitoring")
     */
    public function index()
    {
        $metrik=[];
        $homepage = trim(file_get_contents('http://10.5.95.155/monitor.txt'));
        $moni = explode("//", $homepage);

        foreach ($moni as $line)
        {

            $detail = explode("#", $line);
            foreach ($detail as $subdetail)
            {

                $content = explode(":", $subdetail);

                if(isset($content[1])) $metrik[$content[0]]=$content[1];

            }

        }

        $UsedDisk = substr($metrik["UsedDisk"], 3, -1);
        $bos=100-$UsedDisk;
        $usedPercent=intval((trim($metrik["\nUsedRam"])/trim($metrik["\nTotalRam"]))*100);
        $freePercent=100-$usedPercent;




        $metriko=[];
        $homepages = trim(file_get_contents('http://10.5.95.156/monitor.txt'));
        $monik = explode("//", $homepages);

        foreach ($monik as $linea)
        {

            $details = explode("#", $linea);
            foreach ($details as $subdetails)
            {

                $contents = explode(":", $subdetails);

                if(isset($contents[1])) $metriko[$contents[0]]=$contents[1];

            }

        }
        $UsedDisks = substr($metriko["UsedDisk"], 3, -1);
        $boss=100-$UsedDisks;
        $usedPercents=intval((trim($metriko["\nUsedRam"])/trim($metriko["\nTotalRam"]))*100);
        $freePercents=100-$usedPercents;



        $metrika=[];
        $homepagea = trim(file_get_contents('http://10.5.95.157/monitor.txt'));
        $monika = explode("//", $homepagea);

        foreach ($monika as $lineas)
        {

            $detailss = explode("#", $lineas);
            foreach ($detailss as $subdetailss)
            {

                $contentss = explode(":", $subdetailss);

                if(isset($contentss[1])) $metrika[$contentss[0]]=$contentss[1];

            }

        }
        $UsedDiskss = substr($metrika["UsedDisk"], 3, -1);
        $bosa=100-$UsedDiskss;
        $usedPercentss=intval((trim($metrika["\nUsedRam"])/trim($metriko["\nTotalRam"]))*100);
        $freePercentss=100-$usedPercentss;


        return $this->render('monitoring/index.html.twig', [

            "UsedDisk"=>$UsedDisk,
            "usedPercent"=>$usedPercent,
            "freePercent"=>$freePercent,
            "bos"=>$bos,

            "UsedDisks"=>$UsedDisks,
            "usedPercents"=>$usedPercents,
            "freePercents"=>$freePercents,
            "boss"=>$boss,

            "UsedDiskss"=>$UsedDiskss,
            "usedPercentss"=>$usedPercentss,
            "freePercentss"=>$freePercentss,
            "bosa"=>$bosa,


        ]);
    }
}
