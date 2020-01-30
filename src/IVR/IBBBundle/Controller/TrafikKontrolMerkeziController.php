<?php

namespace App\IVR\IBBBundle\Controller;

use App\Entity\IvrServiceLog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class TrafikKontrolMerkeziController extends AbstractController
{


    /**
     * @Route("/ivr/tkm/{callId}/{ivr}")
     * @param $callId
     * @param $ivr
     * @return Response
     * @throws \Exception
     */
    public function trafikKontrolMerkezi($callId, $ivr)
    {

        $url = $this->getParameter('tkmApiLink').$ivr;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, $url);

        $xmlstr = curl_exec($ch);
        curl_close($ch);

        $em = $this->getDoctrine()->getManager();

        $ivrServiceLog = new IvrServiceLog();
        $ivrServiceLog
            ->setCallId($callId)
            ->setAlias("İbb Menü Trafik Kontrol Merkezi")
            ->setInput($ivr)
            ->setRequest($url)
            ->setResponse($xmlstr)
            ->setCreatesAt(new \DateTime());
        $em->persist($ivrServiceLog);
        $em->flush();


        $xml_parser = xml_parser_create();
        xml_parse_into_struct($xml_parser, $xmlstr, $vals);
        xml_parser_free($xml_parser);
        // wyznaczamy tablice z powtarzajacymi sie tagami na tym samym poziomie
        $_tmp='';
        foreach ($vals as $xml_elem) {
            $x_tag=$xml_elem['tag'];
            $x_level=$xml_elem['level'];
            $x_type=$xml_elem['type'];
            if ($x_level!=1 && $x_type == 'close') {
                if (isset($multi_key[$x_tag][$x_level]))
                    $multi_key[$x_tag][$x_level]=1;
                else
                    $multi_key[$x_tag][$x_level]=0;
            }
            if ($x_level!=1 && $x_type == 'complete') {
                if ($_tmp==$x_tag)
                    $multi_key[$x_tag][$x_level]=1;
                $_tmp=$x_tag;
            }
        }
        // jedziemy po tablicy
        foreach ($vals as $xml_elem) {
            $x_tag=$xml_elem['tag'];
            $x_level=$xml_elem['level'];
            $x_type=$xml_elem['type'];
            if ($x_type == 'open')
                $level[$x_level] = $x_tag;
            $start_level = 1;
            $php_stmt = '$xml_array';
            if ($x_type=='close' && $x_level!=1)
                $multi_key[$x_tag][$x_level]++;
            while ($start_level < $x_level) {
                $php_stmt .= '[$level['.$start_level.']]';
                if (isset($multi_key[$level[$start_level]][$start_level]) && $multi_key[$level[$start_level]][$start_level])
                    $php_stmt .= '['.($multi_key[$level[$start_level]][$start_level]-1).']';
                $start_level++;
            }
            $add='';
            if (isset($multi_key[$x_tag][$x_level]) && $multi_key[$x_tag][$x_level] && ($x_type=='open' || $x_type=='complete')) {
                if (!isset($multi_key2[$x_tag][$x_level]))
                    $multi_key2[$x_tag][$x_level]=0;
                else
                    $multi_key2[$x_tag][$x_level]++;
                $add='['.$multi_key2[$x_tag][$x_level].']';
            }
            if (isset($xml_elem['value']) && trim($xml_elem['value'])!='' && !array_key_exists('attributes', $xml_elem)) {
                if ($x_type == 'open')
                    $php_stmt_main=$php_stmt.'[$x_type]'.$add.'[\'content\'] = $xml_elem[\'value\'];';
                else
                    $php_stmt_main=$php_stmt.'[$x_tag]'.$add.' = $xml_elem[\'value\'];';
                eval($php_stmt_main);
            }
            if (array_key_exists('attributes', $xml_elem)) {
                if (isset($xml_elem['value'])) {
                    $php_stmt_main=$php_stmt.'[$x_tag]'.$add.'[\'content\'] = $xml_elem[\'value\'];';
                    eval($php_stmt_main);
                }
                foreach ($xml_elem['attributes'] as $key=>$value) {
                    $php_stmt_att=$php_stmt.'[$x_tag]'.$add.'[$key] = $value;';
                    eval($php_stmt_att);
                }
            }
        }
//        return $xml_array;

        $a = $xml_array["DATASET"];
        $b = $a["DIFFGR:DIFFGRAM"];
        $c = $b["NEWDATASET"];
        $d = $c["TABLE"];


        $text = "";
        foreach ($d as $data){
//            $e = explode(" ", $data["ROUTE_NAME"]);
            if ($data["CONG_CODE"] == "1"){
                $durum = "Akıcı";
            }elseif ($data["CONG_CODE"] == "2"){
                $durum = "Yoğun";
            }elseif ($data["CONG_CODE"] == "3"){
                $durum = "Açık";
            }
            $text .= ''.$data["LOCATION"].' '.$durum.'. '.'';
        }

        return new Response($text);

//    1 = Akıcı
//    2 = Yoğun
//    3 = Açık

    }

    /**
     * @Route("/ivr/tkmdata/{callId}/{direction}")
     * @param $callId
     * @param $direction
     * @return Response
     * @throws \Exception
     */
    public function trafikKontrolMerkeziData($callId, $direction)
    {

        $url = $this->getParameter('tkmAvayaApiLink');

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, $url);

        $xmlstr = curl_exec($ch);
        curl_close($ch);

        $em = $this->getDoctrine()->getManager();

        $ivrServiceLog = new IvrServiceLog();
        $ivrServiceLog
            ->setCallId($callId)
            ->setAlias("İbb Menü Trafik Kontrol Merkezi")
            ->setInput($direction)
            ->setRequest($url)
            ->setResponse($xmlstr)
            ->setCreatesAt(new \DateTime());
        $em->persist($ivrServiceLog);
        $em->flush();


        $xml_parser = xml_parser_create();
        xml_parse_into_struct($xml_parser, $xmlstr, $vals);
        xml_parser_free($xml_parser);
        // wyznaczamy tablice z powtarzajacymi sie tagami na tym samym poziomie
        $_tmp='';
        foreach ($vals as $xml_elem) {
            $x_tag=$xml_elem['tag'];
            $x_level=$xml_elem['level'];
            $x_type=$xml_elem['type'];
            if ($x_level!=1 && $x_type == 'close') {
                if (isset($multi_key[$x_tag][$x_level]))
                    $multi_key[$x_tag][$x_level]=1;
                else
                    $multi_key[$x_tag][$x_level]=0;
            }
            if ($x_level!=1 && $x_type == 'complete') {
                if ($_tmp==$x_tag)
                    $multi_key[$x_tag][$x_level]=1;
                $_tmp=$x_tag;
            }
        }
        // jedziemy po tablicy
        foreach ($vals as $xml_elem) {
            $x_tag=$xml_elem['tag'];
            $x_level=$xml_elem['level'];
            $x_type=$xml_elem['type'];
            if ($x_type == 'open')
                $level[$x_level] = $x_tag;
            $start_level = 1;
            $php_stmt = '$xml_array';
            if ($x_type=='close' && $x_level!=1)
                $multi_key[$x_tag][$x_level]++;
            while ($start_level < $x_level) {
                $php_stmt .= '[$level['.$start_level.']]';
                if (isset($multi_key[$level[$start_level]][$start_level]) && $multi_key[$level[$start_level]][$start_level])
                    $php_stmt .= '['.($multi_key[$level[$start_level]][$start_level]-1).']';
                $start_level++;
            }
            $add='';
            if (isset($multi_key[$x_tag][$x_level]) && $multi_key[$x_tag][$x_level] && ($x_type=='open' || $x_type=='complete')) {
                if (!isset($multi_key2[$x_tag][$x_level]))
                    $multi_key2[$x_tag][$x_level]=0;
                else
                    $multi_key2[$x_tag][$x_level]++;
                $add='['.$multi_key2[$x_tag][$x_level].']';
            }
            if (isset($xml_elem['value']) && trim($xml_elem['value'])!='' && !array_key_exists('attributes', $xml_elem)) {
                if ($x_type == 'open')
                    $php_stmt_main=$php_stmt.'[$x_type]'.$add.'[\'content\'] = $xml_elem[\'value\'];';
                else
                    $php_stmt_main=$php_stmt.'[$x_tag]'.$add.' = $xml_elem[\'value\'];';
                eval($php_stmt_main);
            }
            if (array_key_exists('attributes', $xml_elem)) {
                if (isset($xml_elem['value'])) {
                    $php_stmt_main=$php_stmt.'[$x_tag]'.$add.'[\'content\'] = $xml_elem[\'value\'];';
                    eval($php_stmt_main);
                }
                foreach ($xml_elem['attributes'] as $key=>$value) {
                    $php_stmt_att=$php_stmt.'[$x_tag]'.$add.'[$key] = $value;';
                    eval($php_stmt_att);
                }
            }
        }
//        return $xml_array;

        $a = $xml_array["DATASET"];
        $b = $a["DIFFGR:DIFFGRAM"];
        $c = $b["NEWDATASET"];
        $d = $c["TABLE1"];


        $text = "";
        foreach ($d as $data){
      //var_dump($data);
   // echo $data["LOCATION"]."<br>";
    //echo $data["DIRECTION"]."<br>";


        if($data["DIRECTION"]==$direction)
        {
           // $e = explode(" ", $data["ROUTE_NAME"]);
            if (isset($data["VALUE"]) == "1"){
                $durum = "Akıcı";
            }elseif (isset($data["VALUE"]) == "2"){
                $durum = "Yoğun";
            }elseif (isset($data["VALUE"]) == "3"){
                $durum = "Açık";
            }
            else {$durum = "Açık";}

            /*
             * if (isset($data["DIRECTION"]) == "1"){
                $yon = "Anadolu Avrupa Yönü";
            }elseif (isset($data["DIRECTION"]) == "2"){
                $yon = "Avrupa Anadolu Yönü";
            }
            else {$durum = "";}
            */

            $text .= ''.$data["LOCATION"].' '.$durum.'. '.'';
        }
   }

        return new Response($text);

//    1 = Akıcı
//    2 = Yoğun
//    3 = Açık



    }

}