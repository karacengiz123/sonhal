<?php

namespace App\Controller;

use App\Entity\BreakGroup;
use App\Form\BreakGroupType;
use App\Repository\BreakGroupRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/break/group")
 */
class BreakGroupController extends AbstractController
{
    /**
     * @IsGranted("ROLE_BREAK_GROUP_INDEX")
     * @Route("/", name="break_group_index", methods={"GET"})
     */
    public function index(BreakGroupRepository $breakGroupRepository): Response
    {
        return $this->render('break_group/index.html.twig', [
            'break_groups' => $breakGroupRepository->findAll(),
        ]);
    }

    /**
     * @IsGranted("ROLE_BREAK_GROUP_NEW")
     * @Route("/new", name="break_group_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $breakGroup = new BreakGroup();
        $form = $this->createForm(BreakGroupType::class, $breakGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($breakGroup);
            $entityManager->flush();

            return $this->redirectToRoute('break_group_index');
        }

        return $this->render('break_group/new.html.twig', [
            'break_group' => $breakGroup,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_BREAK_GROUP_SHOW")
     * @Route("/{id}", name="break_group_show", methods={"GET"})
     */
    public function show(BreakGroup $breakGroup): Response
    {
        return $this->render('break_group/show.html.twig', [
            'break_group' => $breakGroup,
        ]);
    }

    /**
     * @IsGranted("ROLE_BREAK_GROUP_EDIT")
     * @Route("/{id}/edit", name="break_group_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, BreakGroup $breakGroup): Response
    {
        $form = $this->createForm(BreakGroupType::class, $breakGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('break_group_index', [
                'id' => $breakGroup->getId(),
            ]);
        }

        return $this->render('break_group/edit.html.twig', [
            'break_group' => $breakGroup,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_BREAK_GROUP_DELETE")
     * @Route("/{id}", name="break_group_delete", methods={"DELETE"})
     */
    public function delete(Request $request, BreakGroup $breakGroup): Response
    {
        if ($this->isCsrfTokenValid('delete'.$breakGroup->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($breakGroup);
            $entityManager->flush();
        }

        return $this->redirectToRoute('break_group_index');
    }
}
