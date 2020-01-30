<?php

namespace App\IVR\IgdasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SozlesmeIslemleriEskiController extends AbstractController
{

    /**
     * @Route("/ivr/igdas/pbx/contractTransactionssssssssss/{tdcNo}/{aDurumu}")
     * @param Request $request
     * @param $tdcNo
     * @param $aDurumu
     * @return Response
     */
    public function contractTransactionssssssssss(Request $request, $tdcNo, $aDurumu)
    {
        $client = new \SoapClient($this->getParameter('igdasApiLink'));

        $tdcID = $client->mtGetTesisatByTDC(['pTDCno' => $tdcNo]);

        // TDCID Değişim Yeri
        $tdcID = $tdcID->mtGetTesisatByTDCResult->_TDCID;

        $project = $client->mtGetBorcbyTDC(['pTDCID' => $tdcID]);

        if ($aDurumu == "A"){

            if ($project->mtGetBorcbyTDCResult->_BorcVar == true){

                $anons = "Tesisatınızın ".$project->mtGetBorcbyTDCResult->_BorcTutari." türklirası abone bağlantı bedeli borcu bulunmaktadır. Sözleşme yapabilmeniz için bu bedelin ödenmesi gerekmektedir. Abone bağlantı bedeli borcunu en yakın İGDAŞ veznelerinden ödeyebilirsiniz. Bilgileri tekrar dinlemek için biri . Bir üst menüye dönmek için yıldızı . Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız";

                return $this->json([
                    "tdcID" => $tdcID,
                    "aDurumu" => $aDurumu,
                    "anons" => $anons,
                    "guvenceBedeli" => "",
                    "debt" => $project->mtGetBorcbyTDCResult->_BorcVar,
                    "anons_2" => "",
                    "anons_3" => ""
                ]);

            }else{

                if ($project->mtGetBorcbyTDCResult->_EtkinlikKodu == 10 and in_array($project->mtGetBorcbyTDCResult->_KullanimSinifi,[1,2,3,16,17,18,19,20,21])){

                    $anons = "Sözleşmenizi www.igdas.com.tr adresinden E DEVLET e giriş yaparak online veya bölgenizde bulunan İGDAŞ Müşteri Hizmetleri Şefliği ne müracaat ederek yapabilirsiniz. Tesisatınızın sözleşme bedeli hakkında bilgi almak için biri . Sözleşme esnasında istenilen belgeleri öğrenmek için ikiyi . Bir üst menüye dönmek için yıldızı . Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                    $anons_2 = "Tesisatınız için ödenmesi gereken güvence bedeli ".$project->mtGetBorcbyTDCResult->_GuvenceBedeli." türklirasıdır. Bu bedeli sözleşme esnasında peşin olarak ödeye bileceğiniz gibi. faturalarınıza taksitler halinde yansıtılmasını da talep edebilirsiniz. Bir üst menüye dönmek için yıldızı. Ana menüye dönmek için kareyi. Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                    $anons_3 = "Sözleşme için gerekli belgeler şunlardır. TC Kimlik numaralı kimlik kartı. Mülk sahipleri için tapu fotokopisi. kiracılar için kira kontratı fotokopisi. Sözleşmenin üçüncü şahıslar tarafından yapılması durumunda ayrıca noter tasdikli vekâletname. Sözleşme yaptıktan sonra gaz açma işlemleri için tesisatınızı yaptırdığınız firma ile irtibata geçmeniz gerekmektedir. Bir üst menüye dönmek için yıldızı. Ana menüye dönmek için kareyi. Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                    return $this->json([
                        "tdcID" => $tdcID,
                        "aDurumu" => $aDurumu,
                        "anons" => $anons,
                        "guvenceBedeli" => $project->mtGetBorcbyTDCResult->_GuvenceBedeli,
                        "debt" => $project->mtGetBorcbyTDCResult->_BorcVar,
                        "anons_2" => $anons_2,
                        "anons_3" => $anons_3
                    ]);

                }else{

                    if ($project->mtGetBorcbyTDCResult->_EtkinlikKodu == 10 and in_array($project->mtGetBorcbyTDCResult->_KullanimSinifi,[4,5,6,7,10,11,22,23])){

                        $anons = "Sözleşmenizi www.igdas.com.tr adresinden E-DEVLETe giriş yaparak online veya bölgenizde bulunan İGDAŞ Müşteri Hizmetleri Şefliği ne müracaat ederek yapabilirsiniz. Tesisatınızın sözleşme bedeli hakkında bilgi almak için biri . Sözleşme esnasında istenilen belgeleri öğrenmek için ikiyi . Bir üst menüye dönmek için yıldızı . Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                        $anons_2 = "Tesisatınız için ödenmesi gereken güvence bedeli ".$project->mtGetBorcbyTDCResult->_GuvenceBedeli." TL’dir. Bu bedeli sözleşme esnasında peşin olarak ödeye bileceğiniz gibi. faturalarınıza taksitler halinde yansıtılmasını da talep edebilirsiniz. Bir üst menüye dönmek için yıldızı. Ana menüye dönmek için kareyi. Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                        $anons_3 = "Sözleşme için gerekli belgeler şunlardır. Apartman sakinlerinin oy çokluğu ile verdiği yetki kararı fotokopisi ve TC Kimlik numaralı kimlik kartı. Sözleşmenin üçüncü şahıslar tarafından yapılması durumunda ayrıca noter onaylı vekâletname istenmektedir. Sözleşme yaptıktan sonra gaz açma işlemleri için tesisatınızı yaptırdığınız firma ile irtibata geçmeniz gerekmektedir. Bir üst menüye dönmek için yıldızı. Ana menüye dönmek için kareyi. Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                        return $this->json([
                            "tdcID" => $tdcID,
                            "aDurumu" => $aDurumu,
                            "anons" => $anons,
                            "guvenceBedeli" => $project->mtGetBorcbyTDCResult->_GuvenceBedeli,
                            "debt" => $project->mtGetBorcbyTDCResult->_BorcVar,
                            "anons_2" => $anons_2,
                            "anons_3" => $anons_3
                        ]);


                    }else{

                        if (in_array($project->mtGetBorcbyTDCResult->_EtkinlikKodu,[20,75,92]) and in_array($project->mtGetBorcbyTDCResult->_KullanimSinifi,[1,2,3,4,5,6,10,11,16,17,18,19,20,21,22,23,24])){

                            $anons = "Sözleşmenizi size en yakın hizmet binamıza gelerek yapabilirsiniz. Tesisatınızın sözleşme bedeli hakkında bilgi almak için biri . Sözleşme esnasında istenilen belgeleri öğrenmek için ikiyi . Bir üst menüye dönmek için yıldızı .  Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                            $anons_2 = "Tesisatınız için ödenmesi gereken güvence bedeli ".$project->mtGetBorcbyTDCResult->_GuvenceBedeli." türklirasıdır. Bu bedeli sözleşme esnasında peşin olarak ödeye bileceğiniz gibi. faturalarınıza taksitler halinde yansıtılmasını da talep edebilirsiniz. Bir üst menüye dönmek için: yıldızı. Ana menüye dönmek için kareyi. Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                            $anons_3 = "Sözleşme için gerekli belgeler şunlardır. Mülk sahipleri tapu fotokopisi. kiracılar kira kontratı fotokopisi. vergi levhası, yetki belgesi. ticaret sicil gazetesi, imza sirküleri. varsa kaşe ve TC Kimlik numaralı kimlik kartı. Sözleşmenin üçüncü şahıslar tarafından yapılması durumunda ayrıca noter tasdikli vekâletname istenmektedir. Sözleşme yaptıktan sonra gaz açma işlemleri için tesisatınızı yaptırdığınız firma ile irtibata geçmeniz gerekmektedir. Bir üst menüye dönmek için yıldızı. Ana menüye dönmek için kareyi. Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                            return $this->json([
                                "tdcID" => $tdcID,
                                "aDurumu" => $aDurumu,
                                "anons" => $anons,
                                "guvenceBedeli" => $project->mtGetBorcbyTDCResult->_GuvenceBedeli,
                                "debt" => $project->mtGetBorcbyTDCResult->_BorcVar,
                                "anons_2" => $anons_2,
                                "anons_3" => $anons_3
                            ]);


                        }else{

                            if ($project->mtGetBorcbyTDCResult->_EtkinlikKodu == 22 and in_array($project->mtGetBorcbyTDCResult->_KullanimSinifi,[4,5,6,7,8,9,10,11,12,13,14,18,20,21,22,23,24])){

                                $anons = "Sözleşmenizi size en yakın hizmet binamıza gelerek yapabilirsiniz. Tesisatınızın sözleşme bedeli hakkında bilgi almak için biri . Sözleşme esnasında istenilen belgeleri öğrenmek için ikiyi . Bir üst menüye dönmek için yıldızı .  Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                                $anons_2 = "Tesisatınız için ödenmesi gereken güvence bedeli ".$project->mtGetBorcbyTDCResult->_GuvenceBedeli." türklirasıdır. Bu bedeli sözleşme esnasında peşin olarak ödeye bileceğiniz gibi. faturalarınıza taksitler halinde yansıtılmasını da talep edebilirsiniz. Bir üst menüye dönmek için yıldızı. Ana menüye dönmek için kareyi. Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                                $anons_3 = "Sözleşme için gerekli belgeler şunlardır. İş yeri sahiplerinin oy çokluğu ile verdiği yetki kararı fotokopisi ve TC Kimlik numaralı kimlik kartı. vergi levhası. Sözleşmenin üçüncü şahıslar tarafından yapılması durumunda ayrıca noter tasdikli vekâletname istenmektedir. Sözleşme yaptıktan sonra gaz açma işlemleri için tesisatınızı yaptırdığınız firma ile irtibata geçmeniz gerekmektedir. Bir üst menüye dönmek için yıldızı. Ana menüye dönmek için kareyi. Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                                return $this->json([
                                    "tdcID" => $tdcID,
                                    "aDurumu" => $aDurumu,
                                    "anons" => $anons,
                                    "guvenceBedeli" => $project->mtGetBorcbyTDCResult->_GuvenceBedeli,
                                    "debt" => $project->mtGetBorcbyTDCResult->_BorcVar,
                                    "anons_2" => $anons_2,
                                    "anons_3" => $anons_3
                                ]);


                            }else{

                                if (in_array($project->mtGetBorcbyTDCResult->_KullanimSinifi,[40,50,55,60,70,91])){

                                    $anons = "Sözleşmenizi size en yakın hizmet binamıza gelerek yapabilirsiniz. Tesisatınızın sözleşme bedeli hakkında bilgi almak için biri. Sözleşme esnasında istenilen belgeleri öğrenmek için ikiyi . Bir üst menüye dönmek için yıldızı .  Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                                    $anons_2 = "Tesisatınız için ödenmesi gereken güvence bedeli ".$project->mtGetBorcbyTDCResult->_GuvenceBedeli." türklirasıdır. Bu bedeli sözleşme esnasında peşin olarak ödeye bileceğiniz gibi. faturalarınıza taksitler halinde yansıtılmasını da talep edebilirsiniz. Bir üst menüye dönmek için yıldızı. Ana menüye dönmek için kareyi. Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                                    $anons_3 = "Sözleşme için gerekli belgeler şunlardır. Mülk sahipleri tapu fotokopisi. kiracılar kira kontratı fotokopisi. yetki belgesi. varsa kaşe ve TC Kimlik numaralı kimlik kartı. Sözleşmenin üçüncü şahıslar tarafından yapılması durumunda ayrıca noter tasdikli vekâletname istenmektedir. Sözleşme yaptıktan sonra gaz açma işlemleri için tesisatınızı yaptırdığınız firma ile irtibata geçmeniz gerekmektedir. Bir üst menüye dönmek için yıldızı. Ana menüye dönmek için kareyi. Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                                    return $this->json([
                                        "tdcID" => $tdcID,
                                        "aDurumu" => $aDurumu,
                                        "anons" => $anons,
                                        "guvenceBedeli" => $project->mtGetBorcbyTDCResult->_GuvenceBedeli,
                                        "debt" => $project->mtGetBorcbyTDCResult->_BorcVar,
                                        "anons_2" => $anons_2,
                                        "anons_3" => $anons_3
                                    ]);


                                }

                            }

                        }

                    }

                }

            }

        }else{

            if ($aDurumu == "F"){

                if ($project->mtGetBorcbyTDCResult->_EtkinlikKodu == 10 and in_array($project->mtGetBorcbyTDCResult->_KullanimSinifi,[1,2,3,16,17,18,19,20,21])){

                    $anons = "Sözleşmenizi www.igdas.com.tr adresinden E DEVLET e giriş yaparak online veya bölgenizde bulunan İGDAŞ Müşteri Hizmetleri Şefliği ne müracaat ederek yapabilirsiniz. Tesisatınızın sözleşme bedeli hakkında bilgi almak için biri . Sözleşme esnasında istenilen belgeleri öğrenmek için ikiyi . Bir üst menüye dönmek için yıldızı . Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                    $anons_2 = "Tesisatınız için ödenmesi gereken güvence bedeli ".$project->mtGetBorcbyTDCResult->_GuvenceBedeli." türklirasıdır. Bu bedeli sözleşme esnasında peşin olarak ödeye bileceğiniz gibi. faturalarınıza taksitler halinde yansıtılmasını da talep edebilirsiniz. Bir üst menüye dönmek için: yıldızı. Ana menüye dönmek için kareyi. Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                    $anons_3 = "Sözleşme için gerekli belgeler şunlardır. TC Kimlik numaralı kimlik kartı. Mülk sahipleri için tapu fotokopisi. kiracılar için kira kontratı fotokopisi. Sözleşmenin üçüncü şahıslar tarafından yapılması durumunda ayrıca noter tasdikli vekâletname. Sözleşme yaptıktan sonra gaz açma işlemleri için tesisatınızı yaptırdığınız firma ile irtibata geçmeniz gerekmektedir. Bir üst menüye dönmek için yıldızı. Ana menüye dönmek için kareyi. Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                    return $this->json([
                        "tdcID" => $tdcID,
                        "aDurumu" => $aDurumu,
                        "anons" => $anons,
                        "guvenceBedeli" => $project->mtGetBorcbyTDCResult->_GuvenceBedeli,
                        "debt" => $project->mtGetBorcbyTDCResult->_BorcVar,
                        "anons_2" => $anons_2,
                        "anons_3" => $anons_3
                    ]);

                }else{

                    if ($project->mtGetBorcbyTDCResult->_EtkinlikKodu == 10 and in_array($project->mtGetBorcbyTDCResult->_KullanimSinifi,[4,5,6,7,10,11,22,23])){

                        $anons = "Sözleşmenizi www.igdas.com.tr adresinden E DEVLET e giriş yaparak online veya bölgenizde bulunan İGDAŞ Müşteri Hizmetleri Şefliği ne müracaat ederek yapabilirsiniz. Tesisatınızın sözleşme bedeli hakkında bilgi almak için biri . Sözleşme esnasında istenilen belgeleri öğrenmek için ikiyi . Bir üst menüye dönmek için yıldızı . Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                        $anons_2 = "Tesisatınız için ödenmesi gereken güvence bedeli ".$project->mtGetBorcbyTDCResult->_GuvenceBedeli." türklirasıdır. Bu bedeli sözleşme esnasında peşin olarak ödeye bileceğiniz gibi. faturalarınıza taksitler halinde yansıtılmasını da talep edebilirsiniz. Bir üst menüye dönmek için: yıldızı. Ana menüye dönmek için kareyi. Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                        $anons_3 = "Sözleşme için gerekli belgeler şunlardır. Apartman sakinlerinin oy çokluğu ile verdiği yetki kararı fotokopisi ve TC Kimlik numaralı kimlik kartı. Sözleşmenin üçüncü şahıslar tarafından yapılması durumunda ayrıca noter onaylı vekâletname istenmektedir. Sözleşme yaptıktan sonra gaz açma işlemleri için tesisatınızı yaptırdığınız firma ile irtibata geçmeniz gerekmektedir. Bir üst menüye dönmek için yıldızı. Ana menüye dönmek için kareyi. Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                        return $this->json([
                            "tdcID" => $tdcID,
                            "aDurumu" => $aDurumu,
                            "anons" => $anons,
                            "guvenceBedeli" => $project->mtGetBorcbyTDCResult->_GuvenceBedeli,
                            "debt" => $project->mtGetBorcbyTDCResult->_BorcVar,
                            "anons_2" => $anons_2,
                            "anons_3" => $anons_3
                        ]);


                    }else{

                        if (in_array($project->mtGetBorcbyTDCResult->_EtkinlikKodu,[20,75,92]) and in_array($project->mtGetBorcbyTDCResult->_KullanimSinifi,[1,2,3,4,5,6,10,11,16,17,18,19,20,21,22,23,24])){

                            $anons = "Sözleşmenizi size en yakın hizmet binamıza gelerek yapabilirsiniz. Tesisatınızın sözleşme bedeli hakkında bilgi almak için biri . Sözleşme esnasında istenilen belgeleri öğrenmek için ikiyi . Bir üst menüye dönmek için yıldızı .  Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                            $anons_2 = "Tesisatınız için ödenmesi gereken güvence bedeli ".$project->mtGetBorcbyTDCResult->_GuvenceBedeli." türklirasıdır. Bu bedeli sözleşme esnasında peşin olarak ödeye bileceğiniz gibi. faturalarınıza taksitler halinde yansıtılmasını da talep edebilirsiniz. Bir üst menüye dönmek için: yıldızı. Ana menüye dönmek için kareyi. Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                            $anons_3 = "Sözleşme için gerekli belgeler şunlardır. Mülk sahipleri tapu fotokopisi. kiracılar kira kontratı fotokopisi. vergi levhası, yetki belgesi. ticaret sicil gazetesi, imza sirküleri. varsa kaşe ve TC Kimlik numaralı kimlik kartı. Sözleşmenin üçüncü şahıslar tarafından yapılması durumunda ayrıca noter tasdikli vekâletname istenmektedir. Sözleşme yaptıktan sonra gaz açma işlemleri için tesisatınızı yaptırdığınız firma ile irtibata geçmeniz gerekmektedir. Bir üst menüye dönmek için yıldızı. Ana menüye dönmek için kareyi. Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                            return $this->json([
                                "tdcID" => $tdcID,
                                "aDurumu" => $aDurumu,
                                "anons" => $anons,
                                "guvenceBedeli" => $project->mtGetBorcbyTDCResult->_GuvenceBedeli,
                                "debt" => $project->mtGetBorcbyTDCResult->_BorcVar,
                                "anons_2" => $anons_2,
                                "anons_3" => $anons_3
                            ]);


                        }else{

                            if ($project->mtGetBorcbyTDCResult->_EtkinlikKodu == 22 and in_array($project->mtGetBorcbyTDCResult->_KullanimSinifi,[4,5,6,7,8,9,10,11,12,13,14,18,20,21,22,23,24])){

                                $anons = "Sözleşmenizi size en yakın hizmet binamıza gelerek yapabilirsiniz. Tesisatınızın sözleşme bedeli hakkında bilgi almak için biri . Sözleşme esnasında istenilen belgeleri öğrenmek için ikiyi . Bir üst menüye dönmek için yıldızı .  Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                                $anons_2 = "Tesisatınız için ödenmesi gereken güvence bedeli ".$project->mtGetBorcbyTDCResult->_GuvenceBedeli." türklirasıdır. Bu bedeli sözleşme esnasında peşin olarak ödeye bileceğiniz gibi. faturalarınıza taksitler halinde yansıtılmasını da talep edebilirsiniz. Bir üst menüye dönmek için yıldızı. Ana menüye dönmek için kareyi. Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                                $anons_3 = "Sözleşme için gerekli belgeler şunlardır. İş yeri sahiplerinin oy çokluğu ile verdiği yetki kararı fotokopisi ve TC Kimlik numaralı kimlik kartı. vergi levhası. Sözleşmenin üçüncü şahıslar tarafından yapılması durumunda ayrıca noter tasdikli vekâletname istenmektedir. Sözleşme yaptıktan sonra gaz açma işlemleri için tesisatınızı yaptırdığınız firma ile irtibata geçmeniz gerekmektedir. Bir üst menüye dönmek için yıldızı. Ana menüye dönmek için kareyi. Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                                return $this->json([
                                    "tdcID" => $tdcID,
                                    "aDurumu" => $aDurumu,
                                    "anons" => $anons,
                                    "guvenceBedeli" => $project->mtGetBorcbyTDCResult->_GuvenceBedeli,
                                    "debt" => $project->mtGetBorcbyTDCResult->_BorcVar,
                                    "anons_2" => $anons_2,
                                    "anons_3" => $anons_3
                                ]);


                            }else{

                                if (in_array($project->mtGetBorcbyTDCResult->_KullanimSinifi,[40,50,55,60,70,91])){

                                    $anons = "Sözleşmenizi size en yakın hizmet binamıza gelerek yapabilirsiniz. Tesisatınızın sözleşme bedeli hakkında bilgi almak için biri . Sözleşme esnasında istenilen belgeleri öğrenmek için ikiyi . Bir üst menüye dönmek için yıldızı .  Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                                    $anons_2 = "Tesisatınız için ödenmesi gereken güvence bedeli ".$project->mtGetBorcbyTDCResult->_GuvenceBedeli." türklirasıdır. Bu bedeli sözleşme esnasında peşin olarak ödeye bileceğiniz gibi. faturalarınıza taksitler halinde yansıtılmasını da talep edebilirsiniz. Bir üst menüye dönmek için yıldızı. Ana menüye dönmek için kareyi. Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                                    $anons_3 = "Sözleşme için gerekli belgeler şunlardır. Mülk sahipleri tapu fotokopisi. kiracılar kira kontratı fotokopisi. yetki belgesi. varsa kaşe ve TC Kimlik numaralı kimlik kartı. Sözleşmenin üçüncü şahıslar tarafından yapılması durumunda ayrıca noter tasdikli vekâletname istenmektedir. Sözleşme yaptıktan sonra gaz açma işlemleri için tesisatınızı yaptırdığınız firma ile irtibata geçmeniz gerekmektedir. Bir üst menüye dönmek için yıldızı. Ana menüye dönmek için kareyi. Müşteri temsilcisine bağlanmak için lütfen sıfırı tuşlayınız.";

                                    return $this->json([
                                        "tdcID" => $tdcID,
                                        "aDurumu" => $aDurumu,
                                        "anons" => $anons,
                                        "guvenceBedeli" => $project->mtGetBorcbyTDCResult->_GuvenceBedeli,
                                        "debt" => $project->mtGetBorcbyTDCResult->_BorcVar,
                                        "anons_2" => $anons_2,
                                        "anons_3" => $anons_3
                                    ]);


                                }

                            }

                        }

                    }

                }

            }

        }

    }
}