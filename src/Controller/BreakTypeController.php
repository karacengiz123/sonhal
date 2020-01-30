<?php

namespace App\Controller;

use App\Entity\BreakType;
use App\Entity\Group;
use App\Form\BreakTypeType;
use App\Repository\BreakTypeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/break/type")
 */
class BreakTypeController extends AbstractController
{
    /**
     * @IsGranted("ROLE_BREAK_TYPE_INDEX")
     * @Route("/", name="break_type_index", methods={"GET"})
     */
    public function index(BreakTypeRepository $breakTypeRepository): Response
    {
        return $this->render('break_type/index.html.twig', [
            'break_types' => $breakTypeRepository->findAll(),
        ]);
    }

    /**
     * @IsGranted("ROLE_BREAK_TYPE_NEW")
     * @Route("/new", name="break_type_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $breakType = new BreakType();
        $roleGroups = $this->getDoctrine()->getRepository(Group::class)->findAll();
        $roles = [];
        foreach ($roleGroups as $roleGroup){
            $roles[$roleGroup->getName()]=$roleGroup->getName();
        }
        $builder = $this->createFormBuilder($breakType);
        $builder
            ->add('name',null,[
                'label'=>'Mola Adı',
                'attr' => ['class' => 'aaaaaa']
            ])
            ->add('addeableRole',ChoiceType::class,[
                'label'=>'Rol Ekle',
                'choices' => $roles
            ])
        ;
        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($breakType);
            $entityManager->flush();

            return $this->redirectToRoute('break_type_index');
        }

        return $this->render('break_type/new.html.twig', [
            'break_type' => $breakType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_BREAK_TYPE_SHOW")
     * @Route("/{id}", name="break_type_show", methods={"GET"})
     */
    public function show(BreakType $breakType): Response
    {
        return $this->render('break_type/show.html.twig', [
            'break_type' => $breakType,
        ]);
    }

    /**
     * @IsGranted("ROLE_BREAK_TYPE_EDIT")
     * @Route("/{id}/edit", name="break_type_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, BreakType $breakType): Response
    {
        $roleGroups = $this->getDoctrine()->getRepository(Group::class)->findAll();
        $roles = [];
        foreach ($roleGroups as $roleGroup){
            $roles[$roleGroup->getName()]=$roleGroup->getName();
        }
        $builder = $this->createFormBuilder($breakType);
        $builder
            ->add('name')
            ->add('addeableRole',ChoiceType::class,[
                'choices' => $roles
            ])
        ;
        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('break_type_index');
        }

        return $this->render('break_type/edit.html.twig', [
            'break_type' => $breakType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_BREAK_TYPE_DELETE")
     * @Route("/{id}", name="break_type_delete", methods={"DELETE"})
     */
    public function delete(Request $request, BreakType $breakType): Response
    {
        if ($this->isCsrfTokenValid('delete'.$breakType->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($breakType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('break_type_index');
    }
}
