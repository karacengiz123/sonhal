<?php

namespace App\Controller;


use App\Asterisk\Entity\Agents;
use App\Entity\Parse;
use mysql_xdevapi\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;


class ParseController extends AbstractController
{
    /**
     * @Route("/parse")
     */
    public function aaa()
    {

        $entityManager =$this->getDoctrine()->getManager();

        $jsondata = file_get_contents("evaimport/formtemplate.json");
        $json = json_decode($jsondata, true);

        foreach($json as $template)
        {
            if($template['status'] == 'PUBLISHED')
            {
                $publishedArray[] = ['id' => $template['id'],'name' => $template['name'],'description' => $template['description']];
                $parse = new Parse();
                $parse
                    ->setParseID($template['id'])
                    ->setName($template['name'])
                    ->setDescription($template['description']);

                $entityManager->persist($parse);
                try{
                    $entityManager->flush();
                }catch (Exception $e){
                    $e->getMessage();
                }
            }
        }

        return new Response("Kayıt Başarılı");

    }

    /**
     * @Route("/parse-2")
     */
    public function bbb()
    {

        $entityManager =$this->getDoctrine()->getManager();

        $jsondata = file_get_contents("evaimport/templateDetails.json");
        $json = json_decode($jsondata, true);

        $obj = (object) $json;

        $obj_2 = (object) $obj->optionTemplates;

//        dump($obj_2);
        $i = 0;
        foreach ($obj->optionTemplates as $optionTemplates) {

            if ($i > 5)
                continue;

            dump($optionTemplates["id"]);
            dump($optionTemplates["questionTemplate"]["id"]);
            dump($optionTemplates["questionTemplate"]["categoryTemplate"]["id"]);
            dump($optionTemplates["questionTemplate"]["categoryTemplate"]["sectionTemplate"]["id"]);
            dump($optionTemplates["questionTemplate"]["categoryTemplate"]["sectionTemplate"]["formTemplate"]["id"]);
            $i++;

        }
        exit();
    }

//    /**
//     * @Route("/agent")
//     */
//    public function agent(Request $request)
//    {
//        $q=$request->get('q');
//         $asteriskEM=$this->getDoctrine()->getManager('asterisk');
//         $agentsRepo=$asteriskEM->getRepository(Agents::class)->findAll();
//
//         $skill=$agentsRepo->createQueryBuilder('a')
//             ->select('select COUNT(*) as id FROM agents')
//            ->getQuery()->getArrayResult();
//        dump($skill);
//        exit();
////
////        return $this->json($skill);
//    }
}