<?php

namespace App\Controller;

use App\Entity\GuideGroup;
use App\Form\GuideGroupType;
use App\Repository\GuideGroupRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/guidee/group")
 */
class GuideGroupController extends AbstractController
{
    /**
     * @IsGranted("ROLE_GUIDE_GROUP_INDEX")
     * @Route("/", name="guide_group_index", methods={"GET"})
     */
    public function index(GuideGroupRepository $guideGroupRepository): Response
    {
        return $this->render('guide_group/index.html.twig', [
            'guide_groups' => $guideGroupRepository->findAll(),
        ]);
    }

    /**
     * @IsGranted("ROLE_GUIDE_GROUP_NEW")
     * @Route("/new", name="guide_group_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $guideGroup = new GuideGroup();
        $form = $this->createForm(GuideGroupType::class, $guideGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($guideGroup);
            $entityManager->flush();

            return $this->redirectToRoute('guide_group_index');
        }

        return $this->render('guide_group/new.html.twig', [
            'guide_group' => $guideGroup,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_GUIDE_GROUP_SHOW")
     * @Route("/{id}", name="guide_group_show", methods={"GET"})
     */
    public function show(GuideGroup $guideGroup): Response
    {
        return $this->render('guide_group/show.html.twig', [
            'guide_group' => $guideGroup,
        ]);
    }

    /**
     * @IsGranted("ROLE_GUIDE_GROUP_EDIT")
     * @Route("/{id}/edit", name="guide_group_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, GuideGroup $guideGroup): Response
    {
        $form = $this->createForm(GuideGroupType::class, $guideGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('guide_group_index');
        }

        return $this->render('guide_group/edit.html.twig', [
            'guide_group' => $guideGroup,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_GUIDE_GROUP_DELETE")
     * @Route("/{id}", name="guide_group_delete", methods={"DELETE"})
     */
    public function delete(Request $request, GuideGroup $guideGroup): Response
    {
        if ($this->isCsrfTokenValid('delete'.$guideGroup->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($guideGroup);
            $entityManager->flush();
        }

        return $this->redirectToRoute('guide_group_index');
    }
}
