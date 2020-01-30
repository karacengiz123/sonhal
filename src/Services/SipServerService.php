<?php


namespace App\Services;


use App\Asterisk\Entity\Config;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class SipServerService
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function serverName()
    {
        $em = $this->em;

        $config = $em->getRepository(Config::class)->createQueryBuilder("c")
            ->select("c.valueTrunk")
            ->where("c.name=:name")
            ->setParameter("name","temsilci")
            ->getQuery()->getSingleScalarResult();

        $url = "";
        if ($config == "temsilci"){
            $url = "tbxsip.ibb.gov.tr";
        }elseif ($config == "temsilci2"){
            $url = "tbxsip2.ibb.gov.tr";
        }elseif ($config == "temsilcidev"){
            $url = "tbxsipdev.ibb.gov.tr";
        }

        return new Response($url);
    }

}