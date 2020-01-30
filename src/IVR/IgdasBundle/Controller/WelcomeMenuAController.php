<?php

namespace App\IVR\IgdasBundle\Controller;

use App\Entity\IvrServiceLog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WelcomeMenuAController extends AbstractController
{

    /**
     * @Route("/ivr/igdas/pbx/welcomeMenuA/projectStatus/{callId}/{tdcNo}")
     * @param Request $request
     * @param $callId
     * @param $tdcNo
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function welcomeMenuAprojectStatus(Request $request, $callId, $tdcNo)
    {
        $client = new \SoapClient($this->getParameter('igdasApiLink'), array("trace" => 1));
        $em = $this->getDoctrine()->getManager();

        $setTdcNo = $tdcNo;

        $tdcNo = $client->mtGetTesisatByTDC(['pTDCno' => $tdcNo]);
        $ivrServiceLog = new IvrServiceLog();
        $ivrServiceLog
            ->setCallId($callId)
            ->setAlias("İgdaş Menü Karşılama A")
            ->setInput($setTdcNo)
            ->setRequest($client->__getLastRequest())
            ->setResponse($client->__getLastResponse())
            ->setCreatesAt(new \DateTime());
        $em->persist($ivrServiceLog);
        $em->flush();

        $tdcID = $tdcNo->mtGetTesisatByTDCResult->_TDCID;

        $projectStatus = $client->mtGetProjebyTDC(['pTDCID' => $tdcID]);
        $ivrServiceLog = new IvrServiceLog();
        $ivrServiceLog
            ->setCallId($callId)
            ->setAlias("İgdaş Menü Karşılama A")
            ->setInput($tdcID)
            ->setRequest($client->__getLastRequest())
            ->setResponse($client->__getLastResponse())
            ->setCreatesAt(new \DateTime());
        $em->persist($ivrServiceLog);
        $em->flush();

//        dump($projectStatus);
//        exit();

        if($projectStatus->mtGetProjebyTDCResult->_ProjeDurumu == "Proje Verilmemiş"){

            $anons = "Tesisatınız için İGDAŞa henüz proje sunulmamıştır. Lütfen Doğalgaz tesisatınızı sertifikalı firmalara yaptırınız. Tesisatınızı yaptırmış iseniz projenizin durumu ile ilgili  tesisatçı firmanızla irtibata geçiniz. Tesisatçı firmanızın doğalgaz sertifikalı firma amblemli yetkili firma olduğuna dikkat ediniz. Yetkili firmaları www.igdas.com.tr adresindeki sertifikalı firmalar alanından kontrol edebilirsiniz.";

            return $this->json(["tdcID" => $tdcID, "anons" => $anons, "anons_2" => "", "projectStatus" => "V", "debt" => "", "anons_3" => "", "anons_4" => "", "anons_5" => ""]);

        }else{

            if($projectStatus->mtGetProjebyTDCResult->_ProjeDurumu == "Proje Verilmiş ve onaylanmamış"){

                $anons = "Sayın abonemiz. Projeniz ".date('d-m-Y', strtotime($projectStatus->mtGetProjebyTDCResult->_AlinanTarih))." tarihinde İGDAŞ a verilmiş fakat henüz onaylanmamıştır. Projede herhangi bir sorun olmaması durumunda en geç yedi gün içerisinde onaylanmaktadır. Projeniz onaylandıktan sonra sözleşme yapabilirsiniz. Tekrar dinlemek için biri tuşlayınız";

                $debt = $client->mtGetBorcbyTDC(['pTDCID' => $tdcID]);
                $ivrServiceLog = new IvrServiceLog();
                $ivrServiceLog
                    ->setCallId($callId)
                    ->setAlias("İgdaş Menü Karşılama A")
                    ->setInput($tdcID)
                    ->setRequest($client->__getLastRequest())
                    ->setResponse($client->__getLastResponse())
                    ->setCreatesAt(new \DateTime());
                $em->persist($ivrServiceLog);
                $em->flush();

                $debtTrueFalse = $debt->mtGetBorcbyTDCResult->_BorcVar;
                if ($debtTrueFalse == true){
                    $debtTrueFalse = 1;
                }elseif ($debtTrueFalse == false){
                    $debtTrueFalse = 0;
                }

                if ($debt->mtGetBorcbyTDCResult->_BorcVar == true) {

                    $anons_2 = "".date('d-m-Y', strtotime($projectStatus->mtGetProjebyTDCResult->_AlinanTarih))." tarihinde yaptığınız abonelik işleminde ".$debt->mtGetBorcbyTDCResult->_BorcTutari." TL borcunuz bulunmaktadır. Sözleşme yapabilmeniz için bu bedelin ödenmesi gerekmektedir.  Abone bağlantı bedeli borcunu en yakın İGDAŞ veznelerinden ödeyebilirsiniz. Bilgileri tekrar dinlemek için biri. Bir üst menüye dönmek için ikiyi. Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız";

                    return $this->json(["tdcID" => $tdcID, "anons" => $anons, "anons_2" => $anons_2, "projectStatus" => "VO", "debt" => $debtTrueFalse, "anons_3" => "", "anons_4" => "", "anons_5" => ""]);

                }else{

                    $anons_2 = "Projeniz Onaylandıktan Sonra Sözleşmenizi. size en yakın hizmet binamıza gelerek veya www.igdas.com.tr adresindeki internet şubemizi ziyaret ederek yapabilirsiniz. Tekrar dinlemek için biri . Telefonu Kapatmak İçin ikiyi . Müşteri Temsilcisine Bağlanmak İçin sıfırı tuşlayınız.";

                    return $this->json(["tdcID" => $tdcID, "anons" => $anons, "anons_2" => $anons_2, "projectStatus" => "VO", "debt" => $debtTrueFalse, "anons_3" => "", "anons_4" => "", "anons_5" => ""]);

                }

            }else{

                if($projectStatus->mtGetProjebyTDCResult->_ProjeDurumu == "Proje Onaylandı"){

                    $debt = $client->mtGetBorcbyTDC(['pTDCID' => $tdcID]);
                    $ivrServiceLog = new IvrServiceLog();
                    $ivrServiceLog
                        ->setCallId($callId)
                        ->setAlias("İgdaş Menü Karşılama A")
                        ->setInput($tdcID)
                        ->setRequest($client->__getLastRequest())
                        ->setResponse($client->__getLastResponse())
                        ->setCreatesAt(new \DateTime());
                    $em->persist($ivrServiceLog);
                    $em->flush();

                    $debtTrueFalse = $debt->mtGetBorcbyTDCResult->_BorcVar;
                    if ($debtTrueFalse == true){
                        $debtTrueFalse = 1;
                    }elseif ($debtTrueFalse == false){
                        $debtTrueFalse = 0;
                    }

//                    dump($debt);
//                    exit();

                    if ($debt->mtGetBorcbyTDCResult->_BorcVar == true){

                        $anons_2 = "".date('d-m-Y', strtotime($projectStatus->mtGetProjebyTDCResult->_AlinanTarih))." tarihinde yaptığınız abonelik işleminde ".$debt->mtGetBorcbyTDCResult->_BorcTutari." TL borcunuz bulunmaktadır. Sözleşme yapabilmeniz için bu bedelin ödenmesi gerekmektedir.  Abone bağlantı bedeli borcunu en yakın İGDAŞ veznelerinden ödeyebilirsiniz. Bilgileri tekrar dinlemek için biri. Bir üst menüye dönmek için ikiyi. Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız";

                        return $this->json(["tdcID" => $tdcID, "anons" => "", "anons_2" => $anons_2, "projectStatus" => "O", "debt" => $debtTrueFalse, "anons_3" => "", "anons_4" => "", "anons_5" => ""]);

                    }else{

                        if ($debt->mtGetBorcbyTDCResult->_EtkinlikKodu == 10 and in_array($debt->mtGetBorcbyTDCResult->_KullanimSinifi,[1,2,3,16,17,18,19,20,21])){

                            $anons_3 = "Sözleşmenizi www.igdas.com.tr adresinden EDEVLET egiriş yaparak online veya bölgenizde bulunan İGDAŞ Müşteri Hizmetleri Şefliğine müracaat ederek yapabilirsiniz.";

                            $anons_4 = "Tesisatınız için ödenmesi gereken güvence bedeli ".$debt->mtGetBorcbyTDCResult->_GuvenceBedeli." TL dir. Bu bedeli sözleşme esnasında peşin olarak ödeye bileceğiniz gibi faturalarınıza taksitler halinde yansıtılmasını da talep edebilirsiniz.";

                            $anons_5 ="Sözleşme için gerekli belgeler şunlardır TC Kimlik numaralı kimlik kartı. Mülk sahipleri tapu fotokopisi. kiracılar için kira kontratı fotokopisi. Sözleşmenin üçüncü şahıslar tarafından yapılması durumunda ayrıca noter tasdikli vekâletname. Sözleşme yaptıktan sonra gaz açma işlemleri için tesisatınızı yaptırdığınız firma ile irtibata geçmeniz gerekmektedir. Bilgileri tekrar dinlemek için biri. Bir üst menüye dönmek için ikiyi . Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";


                            return $this->json(["tdcID" => $tdcID, "anons_3" => $anons_3, "anons_4" => $anons_4, "anons_5" => $anons_5, "anons_2" => "", "anons" => "", "projectStatus" => "O", "debt" => $debtTrueFalse]);

                        }else{

                            if ($debt->mtGetBorcbyTDCResult->_EtkinlikKodu == 10 and in_array($debt->mtGetBorcbyTDCResult->_KullanimSinifi,[4,5,6,7,10,11,22,23])){

                                $anons_3 = "Sözleşmenizi www.igdas.com.tr adresinden EDEVLET egiriş yaparak online veya bölgenizde bulunan İGDAŞ Müşteri Hizmetleri Şefliğine müracaat ederek yapabilirsiniz.";

                                $anons_4 = "Tesisatınız için ödenmesi gereken güvence bedeli ".$debt->mtGetBorcbyTDCResult->_GuvenceBedeli." TL dir. Bu bedeli sözleşme esnasında peşin olarak ödeye bileceğiniz gibi faturalarınıza taksitler halinde yansıtılmasını da talep edebilirsiniz.";

                                $anons_5 ="Sözleşme için gerekli belgeler şunlardır. Apartman sakinlerinin oy çokluğu ile verdiği yetki kararı fotokopisi. TC Kimlik numaralı kimlik kartı.  Sözleşmenin üçüncü şahıslar tarafından yapılması durumunda ayrıca noter onaylı vekâletname istenmektedir. Sözleşme yaptıktan sonra gaz açma işlemleri için tesisatınızı yaptırdığınız firma ile irtibata geçmeniz gerekmektedir. Bilgileri tekrar dinlemek için biri . Bir üst menüye dönmek için: ikiyi . Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";


                                return $this->json(["tdcID" => $tdcID, "anons_3" => $anons_3, "anons_4" => $anons_4, "anons_5" => $anons_5, "anons_2" => "", "anons" => "", "projectStatus" => "O", "debt" => $debtTrueFalse]);

                            }else{

                                if (in_array($debt->mtGetBorcbyTDCResult->_EtkinlikKodu,[20,75,92]) and in_array($debt->mtGetBorcbyTDCResult->_KullanimSinifi,[1,2,3,4,5,6,10,11,16,17,18,19,20,21,22,23,24])){

                                    $anons_3 = "Sözleşmenizi, size en yakın hizmet binamıza gelerek yapabilirsiniz.";

                                    $anons_4 = "Tesisatınız için ödenmesi gereken güvence bedeli ".$debt->mtGetBorcbyTDCResult->_GuvenceBedeli." TL dir. Bu bedeli sözleşme esnasında peşin olarak ödeye bileceğiniz gibi faturalarınıza taksitler halinde yansıtılmasını da talep edebilirsiniz.";

                                    $anons_5 ="Sözleşme için gerekli belgeler şunlardır. Mülk sahipleri için tapu fotokopisi. kiracılar için kira kontratı fotokopisi. vergi levhası. yetki belgesi. ticaret sicil gazetesi. imza sirküleri. varsa kaşe ve TC Kimlik numaralı kimlik kartı. Sözleşmenin üçüncü şahıslar tarafından yapılması durumunda ayrıca noter tasdikli vekâletname istenmektedir. Sözleşme yaptıktan sonra gaz açma işlemleri için tesisatınızı yaptırdığınız firma ile irtibata geçmeniz gerekmektedir. Bilgileri tekrar dinlemek için biri . Bir üst menüye dönmek için: ikiyi . Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";


                                    return $this->json(["tdcID" => $tdcID, "anons_3" => $anons_3, "anons_4" => $anons_4, "anons_5" => $anons_5, "anons_2" => "", "anons" => "", "projectStatus" => "O", "debt" => $debtTrueFalse]);

                                }else{

                                    if ($debt->mtGetBorcbyTDCResult->_EtkinlikKodu == 22 and in_array($debt->mtGetBorcbyTDCResult->_KullanimSinifi,[1,2,3,4,5,6,10,11,16,17,18,19,20,21,22,23,24])){

                                            $anons_3 = "Sözleşmenizi, size en yakın hizmet binamıza gelerek yapabilirsiniz.";

                                        $anons_4 = "Tesisatınız için ödenmesi gereken güvence bedeli ".$debt->mtGetBorcbyTDCResult->_GuvenceBedeli." TL dir. Bu bedeli sözleşme esnasında peşin olarak ödeye bileceğiniz gibi faturalarınıza taksitler halinde yansıtılmasını da talep edebilirsiniz.";

                                        $anons_5 ="Sözleşme için gerekli belgeler şunlardır. İş yeri sahiplerinin oy çokluğu ile verdiği yetki kararı fotokopisi. TC Kimlik numaralı kimlik kartı. Sözleşmenin üçüncü şahıslar tarafından yapılması durumunda ayrıca noter tasdikli vekâletname istenmektedir. Sözleşme yaptıktan sonra gaz açma işlemleri için tesisatınızı yaptırdığınız firma ile irtibata geçmeniz gerekmektedir. Bilgileri tekrar dinlemek için biri. Bir üst menüye dönmek için: ikiyi . Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";


                                        return $this->json(["tdcID" => $tdcID, "anons_3" => $anons_3, "anons_4" => $anons_4, "anons_5" => $anons_5, "anons_2" => "", "anons" => "", "projectStatus" => "O", "debt" => $debtTrueFalse]);

                                    }else{

                                        if (in_array($debt->mtGetBorcbyTDCResult->_EtkinlikKodu,[40,50,55,60,70,91])){

                                            $anons_3 = "Sözleşmenizi, size en yakın hizmet binamıza gelerek yapabilirsiniz.";

                                            $anons_4 = "Tesisatınız için ödenmesi gereken güvence bedeli ".$debt->mtGetBorcbyTDCResult->_GuvenceBedeli." TL dir. Bu bedeli sözleşme esnasında peşin olarak ödeye bileceğiniz gibi faturalarınıza taksitler halinde yansıtılmasını da talep edebilirsiniz.";

                                            $anons_5 ="Sözleşme için gerekli belgeler şunlardır. Mülk sahipleri için tapu fotokopisi. kiracılar için kira kontratı fotokopisi. yetki belgesi. varsa kaşe. TC Kimlik numaralı kimlik kartı Sözleşmenin üçüncü şahıslar tarafından yapılması durumunda ayrıca noter tasdikli vekâletname istenmektedir. Sözleşme yaptıktan sonra gaz açma işlemleri için tesisatınızı yaptırdığınız firma ile irtibata geçmeniz gerekmektedir. Bilgileri tekrar dinlemek için biri . Bir üst menüye dönmek için: ikiyi . Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";


                                            return $this->json(["tdcID" => $tdcID, "anons_3" => $anons_3, "anons_4" => $anons_4, "anons_5" => $anons_5, "anons_2" => "", "anons" => "", "projectStatus" => "O", "debt" => $debtTrueFalse]);

                                        }

                                    }

                                }

                            }

                        }

                    }

                }

            }
        }

    }

}