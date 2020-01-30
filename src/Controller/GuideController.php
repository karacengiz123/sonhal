<?php

namespace App\Controller;

use App\Asterisk\Entity\Targets;
use App\Entity\Guide;
use App\Entity\GuideGroup;
use App\Form\GuideType;
use App\Repository\GuideRepository;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * @Route("/guide")
 */
class GuideController extends AbstractController
{
    /**
     * @IsGranted("ROLE_GUIDE_INDEX")
     * @Route("/", name="guide_index", methods={"GET"})
     */
    public function index(GuideRepository $guideRepository): Response
    {
        return $this->render('guide/index.html.twig', [
            'guides' => $guideRepository->findAll(),
        ]);
    }

    /**
     * @IsGranted("ROLE_GUIDE_SHOW_GROUP")
     * @Route("/show-group/{id}", name="guide_show_group", methods={"GET"})
     * @param $id
     * @return Response
     */
    public function showGroup($id)
    {
        $guideGroup = $this->getDoctrine()->getRepository(GuideGroup::class)->find($id);
        $guide = $this->getDoctrine()->getRepository(Guide::class)->findBy(["guideGroupID"=>$guideGroup]);
        return $this->render('guide/index.html.twig', [
            'guides' => $guide,
        ]);
    }

    /**
     * @IsGranted("ROLE_GUIDE_NEW")
     * @Route("/new", name="guide_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $target =new Targets();
        $guide = new Guide();
        //$form = $this->createForm(GuideType::class, $guide);
        $targets=$this->getDoctrine()->getRepository(Targets::class)->createQueryBuilder("t")
            ->select('t.targetType','t.description')
            ->getQuery()->getArrayResult();

        $arr2 = [];
        foreach ($targets as $ee){
            $arr2 [$ee["description"]]=$ee["targetType"];


        }


        $target1=array("Seçiniz"=>"");
        $target1=$target1+$arr2;


        $qfb=$this->createFormBuilder($guide);
        $qfb->add('phone',TextType::class, [
            "label" => "Telefon No",
            "attr" => ["class"=>"form-control"]
        ])
            ->add('nameSurname',TextType::class, [
                "label" => "Adı Soyadı",
                "attr" => ["class"=>"form-control"]
            ])
            ->add('guideGroupID',null, [
                "label" => "Grup id",
                "attr" => ["class"=>"form-control"]
            ])
            ->add('title',TextType::class, [
                "label" => "Ünvan",
                "attr" => ["class"=>"form-control"]
            ])
            ->add('targetType',ChoiceType::class, [
                "label" => "Hedef Tipi",
                "attr" => ["class"=>"form-control"],
                'choices' => $target1,
            ])
            ->add('targetId',TextType::class, [
                "label" => "Hedef ID'si",
                "attr" => ["class"=>"form-control"]
            ])
        ;
        $form=$qfb->getForm();






        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($guide);
            $entityManager->flush();

            return $this->redirectToRoute('guide_index');
        }

        return $this->render('guide/new.html.twig', [
            'guide' => $guide,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_GUIDE_SHOW")
     * @Route("/{id}", name="guide_show", methods={"GET"})
     */
    public function show(Guide $guide): Response
    {
        return $this->render('guide/show.html.twig', [
            'guide' => $guide,
        ]);
    }

    /**
     * @IsGranted("ROLE_GUIDE_EDIT")
     * @Route("/{id}/edit", name="guide_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Guide $guide): Response
    {   $target =new Targets();

       // $form = $this->createForm(GuideType::class, $guide);

        $targets=$this->getDoctrine()->getRepository(Targets::class)->createQueryBuilder("t")
            ->select('t.targetType','t.description')
            ->getQuery()->getArrayResult();

        $arr2 = [];
        foreach ($targets as $ee){
            $arr2 [$ee["description"]]=$ee["targetType"];


        }


        $target1=array("Seçiniz"=>"");
        $target1=$target1+$arr2;


        $qfb=$this->createFormBuilder($guide);
        $qfb->add('phone',TextType::class, [
        "label" => "Telefon No",
        "attr" => ["class"=>"form-control"]
    ])
        ->add('nameSurname',TextType::class, [
            "label" => "Adı Soyadı",
            "attr" => ["class"=>"form-control"]
        ])
        ->add('guideGroupID',null, [
            "label" => "Grup id",
            "attr" => ["class"=>"form-control"]
        ])
        ->add('title',TextType::class, [
            "label" => "Ünvan",
            "attr" => ["class"=>"form-control"]
        ])
        ->add('targetType',ChoiceType::class, [
            "label" => "Hedef Tipi",
            "attr" => ["class"=>"form-control"],
            'choices' => $target1,

        ])
        ->add('targetId',NumberType::class, [
            "label" => "Hedef Numarası",
            "attr" => ["class"=>"form-control"]
        ])
    ;
        $form=$qfb->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('guide_index');
        }

        return $this->render('guide/edit.html.twig', [
            'guide' => $guide,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_GUIDE_DELETE")
     * @Route("/{id}", name="guide_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Guide $guide): Response
    {
        if ($this->isCsrfTokenValid('delete'.$guide->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($guide);
            $entityManager->flush();
        }

        return $this->redirectToRoute('guide_index');
    }
}
