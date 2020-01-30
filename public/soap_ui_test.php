<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
function file_get_contents_curl($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}
//echo file_get_contents_curl("http://uat-app-uiwt.belbim.local/EParaAdapter/SoapAdapter/IVRService.asmx?WSDL");
//echo file_get_contents_curl($this->getParameter('igdasApiLink'));
echo file_get_contents_curl("https://osb-mwwebgate.ibb.gov.tr/Internal/Sms/SmsService/ProxyService/SmsServiceSOAPPS?wsdl");
exit;



