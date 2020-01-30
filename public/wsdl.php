<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
/**
 * Created by PhpStorm.
 * User: sarpdoruk
 * Date: 03.12.2018
 * Time: 13:54
 */
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


echo file_get_contents_curl("http://dev-web-uiwt.belbim.local/EParaAdapter/SoapAdapter/IVRService.asmx?WSDL");
exit;

exit;
$client = new \SoapClient("http://iigcrmiis.igdas.com.tr:85/wsBBSIgdasCRMIVR.asmx?WSDL");

$tesisatlar = $client->mtGetTesisatByTelefon(['pTelefonNo' => '48706246704']);
$tesisatInfo = $tesisatlar->mtGetTesisatByTCnoResult->_TesisatRecords->TesisatInfo;


echo "<pre>";
print_r($tesisatInfo);

print_r($client->mtCreateCaseForDesktop(
    [
        'pTDCID' => '',
        'pAccountID' => '',
        'pTelefonNo' => '',
        'pBaslik' => 'İHBAR Normal',
        'pTalepAltKonu' => '',
        'pIVRDurumKodu' => '187',
        'pSon3Menu' => '4',
        'pTalepTipi' => 'Talep',
        'pState' => 'Active',
        'pAgent' => 'testcrma',
        'pCallID' => uniqid(),
        'pAciklama' => 'Açıklıyorum',
    ]
));
echo "</pre>";

//
//QuailityForm
//   formType 1: Çağrı Değerlendirme 2: Çözümdeğerlendirme
//   FormAdı
//
//   QuestionGroup
//     name
//
//     Question:
//        tipi: evet Hayır / Derecelendirme
//        değer: {30 , 25 ,15  , 0}
//            SubQuestion
//                name
//                point
//
//                TRegtert
//                   formid
//                   değerlendire
//                   değerlendirilen
//                   identify
//                   not
//                   image
//
//                   question_id
//                   review_id
//                   point
//
//                   sub_question_id
//                   review_id
//                   pointe
//