<?php

function checkItaxi($dts_from) {
$header=[
            'apikey: jQmt1055jbbGjWeGJAQ4knAdqJ3auaMr',
            'Content-Type: application/json',
            'Accept: application/json'
        ];

		$queryJson = '{

			  "body":{

				 "PhoneNumber": "+'.$dts_from.'",

				 "SpecialCase": "Taksi Şoförü"

			}

			}';
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL,"https://mobilservis.ibb.gov.tr/crm-test/siebel-rest/v1.0/service/RESTInvokeBS/ContactQuery");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $queryJson);

		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response =  $server_output = curl_exec($ch);
		curl_close ($ch);
		return json_decode($response)->Succes;
		
}

if(checkItaxi('905072415959'))
{ echo "numara var"; }