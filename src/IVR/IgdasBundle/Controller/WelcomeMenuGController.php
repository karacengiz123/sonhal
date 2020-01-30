<?php

namespace App\IVR\IgdasBundle\Controller;

use App\Entity\IvrServiceLog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class WelcomeMenuGController extends AbstractController
{

    /**
     * @Route("/ivr/igdas/pbx/welcomeMenuG/invoiceDebt/{callId}/{tdcID}")
     * @param Request $request
     * @param $callId
     * @param $tdcID
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function welcomeMenuGinvoiceDebt(Request $request, $callId, $tdcID)
    {
        $client = new \SoapClient($this->getParameter('igdasApiLink'), array("trace" => 1));
        $em = $this->getDoctrine()->getManager();

//        $invoice_debt = $client->mtGetFaturaBorcKLByTDC(['pTDCID' => $tdcID]);
        $invoice_debt = $client->mtGetToplamBorc(['pTDCID' => $tdcID]);
        $ivrServiceLog = new IvrServiceLog();
        $ivrServiceLog
            ->setCallId($callId)
            ->setAlias("İgdaş Menü Karşılama G - Borç Sorgulama")
            ->setInput($tdcID)
            ->setRequest($client->__getLastRequest())
            ->setResponse($client->__getLastResponse())
            ->setCreatesAt(new \DateTime());
        $em->persist($ivrServiceLog);
        $em->flush();

//            dump($invoice_debt);
//            exit();

            if ($invoice_debt->mtGetToplamBorcResult->_Adettoplam == "0") {

                $anons = "Ödenmemiş Fatura Borcunuz Bulunmamaktadır. Kazı işlemleri gaz yokluğu ve ihbar kaydı bırakmak için dördü. Fatura Talebinde Bulunmak İçin biri. Faturalarınızı e-mail ile almak için ikiyi. Sabit ödeme hakkında bilgi almak için üçü. Sözleşmenizi Sonlandırmak ve Sözleşme Fesih İşlemleri hakıkında bilgi almak için beşi.  Bilgileri tekrar dinlemek için yıldızı. Ana menü için kareyi tuşlayınız.";

                return $this->json(["tdcID" => $tdcID, "anons" => $anons, "debtPcs" => $invoice_debt->mtGetToplamBorcResult->_Adettoplam, "invoiceList" => ""]);

            }else{

                $anons = "Sayın abonemiz ".$invoice_debt->mtGetToplamBorcResult->_Adettoplam." adet ödenmemiş  ".$invoice_debt->mtGetToplamBorcResult->_Borctoplam." TL toplam fatura borcunuz bulunmaktadır. Faturalarınızın ayrıntısını öğrenmek için biri.  Son faturanızı e-mail ile almak için üçü. Sabit ödeme hakkında bilgi almak için dördü. Ana menü için lütfen bekleyiniz.";

                if ($invoice_debt->mtGetToplamBorcResult->_Adettoplam == 0){

                    $invoiceList = "Sayın abonemiz sistemimizde Borç Kayıtlı Faturanız Bulunmamaktadır.";

                }elseif($invoice_debt->mtGetToplamBorcResult->_Adettoplam == 1) {

//                    $invoiceList = "Son ödeme tarihi " . date('d-m-Y', strtotime($invoice_debt->mtGetToplamBorcResult->_FaturaDetaylari->BBSIVRFaturatoplamRecord->_SonOdemeTarihi)) . " . fatura tutarı " . $invoice_debt->mtGetToplamBorcResult->_FaturaDetaylari->BBSIVRFaturatoplamRecord->_FaturaTutari . " . TL dir. Fatura bilginizi tekrar dinlemek için 1 i tuşlayınız.";
                    $invoiceList = "Son ödeme tarihi " . date('d-m-Y', strtotime($invoice_debt->mtGetToplamBorcResult->_SonOdemeTarihi)) . " . fatura tutarı " . $invoice_debt->mtGetToplamBorcResult->_FaturaTutar . " . TL dir. Fatura bilginizi tekrar dinlemek için biri tuşlayınız.";

                }elseif ($invoice_debt->mtGetToplamBorcResult->_Adettoplam > 1){

                    if ($invoice_debt->mtGetToplamBorcResult->_FaturaAdediguncel == 1 and $invoice_debt->mtGetToplamBorcResult->_FaturaAdedigecmis == 1){
                        $invoiceList = "";
                        $invoiceList .= "Son ödeme tarihi " . date('d-m-Y', strtotime($invoice_debt->mtGetToplamBorcResult->_SonOdemeTarihi)) . " . fatura tutarı " . $invoice_debt->mtGetToplamBorcResult->_FaturaTutar . " . TL dir. ";
                        $invoiceList .= "Son ödeme tarihi " . date('d-m-Y', strtotime($invoice_debt->mtGetToplamBorcResult->_FaturaDetaylari->BBSIVRFaturatoplamRecord->_SonOdemeTarihi)) . " . fatura tutarı " . $invoice_debt->mtGetToplamBorcResult->_FaturaDetaylari->BBSIVRFaturatoplamRecord->_FaturaTutari . " . TL dir. Fatura bilginizi tekrar dinlemek için biri tuşlayınız.";
                    }elseif ($invoice_debt->mtGetToplamBorcResult->_FaturaAdediguncel == 1 and $invoice_debt->mtGetToplamBorcResult->_FaturaAdedigecmis > 1){
                        $invoiceList = "";
                        $invoiceList .= "Son ödeme tarihi " . date('d-m-Y', strtotime($invoice_debt->mtGetToplamBorcResult->_SonOdemeTarihi)) . " . fatura tutarı " . $invoice_debt->mtGetToplamBorcResult->_FaturaTutar . " . TL dir. ";
                        $a = $invoice_debt->mtGetToplamBorcResult->_FaturaDetaylari->BBSIVRFaturatoplamRecord;

                        foreach ($a as $b){
                            $invoiceList.="Son ödeme tarihi ".date('d-m-Y',strtotime($b->_SonOdemeTarihi))." . fatura tutarı ".$b->_FaturaTutari." . TL dir. ";
                        }

                        $invoiceList = "".$invoiceList." Fatura bilginizi tekrar dinlemek için biri tuşlayınız.";
                    }elseif ($invoice_debt->mtGetToplamBorcResult->_FaturaAdediguncel == 0 and $invoice_debt->mtGetToplamBorcResult->_FaturaAdedigecmis > 1){
                        $a = $invoice_debt->mtGetToplamBorcResult->_FaturaDetaylari->BBSIVRFaturatoplamRecord;

                        $invoiceList = "";
                        foreach ($a as $b){
                            $invoiceList.="Son ödeme tarihi ".date('d-m-Y',strtotime($b->_SonOdemeTarihi))." . fatura tutarı ".$b->_FaturaTutari." . TL dir. ";
                        }

                        $invoiceList = "".$invoiceList." Fatura bilginizi tekrar dinlemek için biri tuşlayınız.";
                    }
                }

                return $this->json(["tdcID" => $tdcID, "anons" => $anons, "debtPcs" => $invoice_debt->mtGetToplamBorcResult->_Adettoplam, "invoiceList" => $invoiceList]);

            }

    }


    /**
     * @Route("/ivr/igdas/pbx/welcomeMenuG/billRequest/{callId}/{tdcNo}")
     * @param Request $request
     * @param $callId
     * @param $tdcNo
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function welcomeMenuGbillRequest(Request $request, $callId, $tdcNo)
    {
        $client = new \SoapClient($this->getParameter('igdasApiLink'), array("trace" => 1));
        $em = $this->getDoctrine()->getManager();

//        $bill_request = $client->mtGetFaturaDetayByTDC(['pTDCID' => $tdcID]);
        $bill_request = $client->mtGetOkumaTarihi(['pSozlesmeHesapNo' => $tdcNo]);
        $ivrServiceLog = new IvrServiceLog();
        $ivrServiceLog
            ->setCallId($callId)
            ->setAlias("İgdaş Menü Karşılama G - Fatura Talep")
            ->setInput($tdcNo)
            ->setRequest($client->__getLastRequest())
            ->setResponse($client->__getLastResponse())
            ->setCreatesAt(new \DateTime());
        $em->persist($ivrServiceLog);
        $em->flush();

        setlocale(LC_ALL, 'tr_TR.UTF-8');
        header('content-type:text/html;charset=utf-8');
        $firstdate = date("d-m-Y",strtotime($bill_request->mtGetOkumaTarihiResult->_ilktarih));
        $lastdate = date("d-m-Y",strtotime($bill_request->mtGetOkumaTarihiResult->_sontarih));
        $date = date("d-m-Y");

//        dump($firstdate);
//        dump($date);
//        dump($lastdate);

//        dump(strtotime($firstdate) > strtotime($date));
//        exit();


        if (strtotime($firstdate) > strtotime($date)){
            $anons = "Sayın abonemiz bu ayki fatura okumanız  ".strftime("%e %B %Y . ", strtotime(substr($bill_request->mtGetOkumaTarihiResult->_ilktarih,0,10)))." ile ".strftime("%e %B %Y . ", strtotime(substr($bill_request->mtGetOkumaTarihiResult->_sontarih,0,10)))." tarihleri arasında yapılacaktır. Ana menu için lütfen bekleyiniz.";
            return $this->json(["anons" => $anons, "menuClick" => 0]);
        }elseif (strtotime($firstdate) < strtotime($date) and strtotime($date) < strtotime($lastdate)){
            $anons = "Sayın abonemiz bu ayki faturanızı çıkartmak için sayacınız 5 gün içinde okunacaktır. Ana menu için lütfen bekleyiniz.";
            return $this->json(["anons" => $anons, "menuClick" => 0]);
        }elseif (strtotime($lastdate) < strtotime($date)){
            $anons = "Sayın abonemiz bu ayki faturanızı çıkartmak için  ".strftime("%e %B %Y . ", strtotime(substr($bill_request->mtGetOkumaTarihiResult->_okumatarihi,0,10)))." tarihinde sayacınız okunmaya gelinmiş fakat ".$bill_request->mtGetOkumaTarihiResult->_okumatarihi." nedeninden dolayı faturanız çıkartılamamıştır. Doğalgaz sayacındaki kullanım göstergenizi biliyorsanız Gösterge bildirerek fatura çıkarttırmak için 1 i. tuşlayınız.Ana menu için lütfen bekleyiniz.";
            return $this->json(["anons" => $anons, "menuClick" => 1]);
        }
    }


    /**
     * @Route("/ivr/igdas/pbx/welcomeMenuG/sendMail/{callId}/{tdcID}")
     * @param Request $request
     * @param $callId
     * @param $tdcID
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function welcomeMenuGsendMail(Request $request, $callId, $tdcID)
    {
        $client = new \SoapClient($this->getParameter('igdasApiLink'), array("trace" => 1));
        $em = $this->getDoctrine()->getManager();

        $bill_request = $client->mtGetCariBilgileriByTDCID(['pTDCID' => $tdcID]);
        $ivrServiceLog = new IvrServiceLog();
        $ivrServiceLog
            ->setCallId($callId)
            ->setAlias("İgdaş Menü Karşılama G - Mail Gönderimi")
            ->setInput($tdcID)
            ->setRequest($client->__getLastRequest())
            ->setResponse($client->__getLastResponse())
            ->setCreatesAt(new \DateTime());
        $em->persist($ivrServiceLog);
        $em->flush();

        $email = $bill_request->mtGetCariBilgileriByTDCIDResult->_Email;

        if ($email == "0"){

            $anons = "Sayın abonemiz sistemimizde kayıtlı meil adresiniz bulunmamaktadır. Sizi Müşteri temsilcisine aktarıyorum lütfen bekleyiniz.";

            return $this->json(["tdcID" => $tdcID, "anons" => $anons, "email" => 0]);

        }else{

            $anons = "Sayın abonemiz sistemimizde kayıtlı. ".$email." .meil adresinize faturanız gönderilmiştir.";

            return $this->json(["tdcID" => $tdcID, "anons" => $anons, "email" => $email]);

        }
    }

}