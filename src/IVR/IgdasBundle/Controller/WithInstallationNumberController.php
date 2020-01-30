<?php

namespace App\IVR\IgdasBundle\Controller;

use App\Entity\IvrServiceLog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WithInstallationNumberController extends AbstractController
{

    /**
     * @Route("/ivr/igdas/pbx/withInstallationNumber/tdc/{callId}/{tdcNo}")
     * @param Request $request
     * @param $callId
     * @param $tdcNo
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function ws004(Request $request, $callId, $tdcNo)
    {

        // Bireysel = 1
        // Ticari = 2

        $client = new \SoapClient($this->getParameter('igdasApiLink'), array("trace" => 1));
        $em = $this->getDoctrine()->getManager();

        if (strlen($tdcNo) == 12) {
            $customer_1 = $client->mtGetTesisatByTDC(['pTDCno' => $tdcNo]);
            $ivrServiceLog = new IvrServiceLog();
            $ivrServiceLog
                ->setCallId($callId)
                ->setAlias("İgdaş Menü Tesisatlı İşlemler")
                ->setInput($tdcNo)
                ->setRequest($client->__getLastRequest())
                ->setResponse($client->__getLastResponse())
                ->setCreatesAt(new \DateTime());
            $em->persist($ivrServiceLog);
            $em->flush();

            $customerInfo = $client->mtGetCariBilgileriByTDCID(['pTDCID' => $customer_1->mtGetTesisatByTDCResult->_TDCID]);
            $ivrServiceLog = new IvrServiceLog();
            $ivrServiceLog
                ->setCallId($callId)
                ->setAlias("İgdaş Menü Tesisatlı İşlemler")
                ->setInput($customer_1->mtGetTesisatByTDCResult->_TDCID)
                ->setRequest($client->__getLastRequest())
                ->setResponse($client->__getLastResponse())
                ->setCreatesAt(new \DateTime());
            $em->persist($ivrServiceLog);
            $em->flush();

//            dump($customer_1);
//            exit();

            if ($customerInfo->mtGetCariBilgileriByTDCIDResult->_kullanicitipi == 1){

                if ($customerInfo->mtGetCariBilgileriByTDCIDResult->_TcNo == 0 or $customerInfo->mtGetCariBilgileriByTDCIDResult->_TcNo == 11111111111){

                    $anos = "Sayın Abonemiz sistemimizde TC kimlik numaranız kayıtlı değildir. Size daha hızlı hizmet verebilmemiz ve güvenli işlem yapabilmemiz için Lütfen TC kimlik numarası bilginizi güncelleyiniz. TC kimlik numaranızı güncellemek için biri tuşlayınız.";

                    return $this->json(["anons" => $anos, "result" => "TC"]);

                }elseif ($customerInfo->mtGetCariBilgileriByTDCIDResult->_TelNo == 0 or $customerInfo->mtGetCariBilgileriByTDCIDResult->_TelNo == 5999999999){

                    $anos = "Sayın Abonemiz sistemimizde Cep Telefonu numaranız kayıtlı değildir. Size daha hızlı hizmet verebilmemiz ve güvenli işlem yapabilmemiz için Lütfen Cep Telefonu numaranızı ve iletişim bilgilerinizi güncelleyiniz. Cep Telefonu bilginizi güncellemek için biri tuşlayınız.";

                    return $this->json(["anons" => $anos, "result" => "CEPTEL"]);

                }

            }elseif ($customerInfo->mtGetCariBilgileriByTDCIDResult->_kullanicitipi == 2){

                if ($customerInfo->mtGetCariBilgileriByTDCIDResult->_VergiNo == 0){

                    $anos = "Sayın Abonemiz sistemimizde Vergi Numaranız kayıtlı değildir. Size daha hızlı hizmet verebilmemiz ve güvenli işlem yapabilmemiz için Lütfen Vergi Numarası bilginizi güncelleyiniz. Vergi Numarası güncellemek için biri tuşlayınız.";

                    return $this->json(["anons" => $anos, "result" => "VERGİNO"]);

                }elseif ($customerInfo->mtGetCariBilgileriByTDCIDResult->_TelNo == 0 or $customerInfo->mtGetCariBilgileriByTDCIDResult->_TelNo == 5999999999){

                    $anos = "Sayın Abonemiz sistemimizde Cep Telefonu numaranız kayıtlı değildir. Size daha hızlı hizmet verebilmemiz ve güvenli işlem yapabilmemiz için Lütfen Cep Telefonu numaranızı ve iletişim bilgilerinizi güncelleyiniz. Cep Telefonu bilginizi güncellemek için biri tuşlayınız.";

                    return $this->json(["anons" => $anos, "result" => "CEPTEL"]);

                }

            }

            return $this->json(["anons" => "", "result" => "GÜNCEL"]);

        }

    }

}