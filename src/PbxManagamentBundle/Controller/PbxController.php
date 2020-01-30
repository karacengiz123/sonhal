<?php


namespace App\PbxManagamentBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PbxController extends AbstractController
{
    /**
     * @Route("pbx-managament", name="pbx_managament_home_page")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function pbxManagamentHomePage()
    {
        return $this->render("@PbxManagament/pages/index.html.twig");
    }

    /**
     * @Route("pbx-deneme",name="pbx_deneme")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function denemePbxManagament()
    {
        $alertTitle="Deneme";



        return $this->render("@PbxManagament/pages/deneme.html.twig",[
            "alertTitle"=>$alertTitle
        ]);
    }




}