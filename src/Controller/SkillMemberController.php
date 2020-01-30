<?php

namespace App\Controller;

use App\Entity\SkillMember;
use App\Form\SkillMemberType;
use App\Repository\SkillMemberRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/skill/member")
 */
class SkillMemberController extends AbstractController
{
    /**
     * @Route("/", name="skill_member_index", methods={"GET"})
     */
    public function index(SkillMemberRepository $skillMemberRepository): Response
    {
        return $this->render('skill_member/index.html.twig', [
            'skill_members' => $skillMemberRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="skill_member_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $skillMember = new SkillMember();
        $form = $this->createForm(SkillMemberType::class, $skillMember);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($skillMember);
            $entityManager->flush();

            return $this->redirectToRoute('skill_member_index');
        }

        return $this->render('skill_member/new.html.twig', [
            'skill_member' => $skillMember,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="skill_member_show", methods={"GET"})
     */
    public function show(SkillMember $skillMember): Response
    {
        return $this->render('skill_member/show.html.twig', [
            'skill_member' => $skillMember,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="skill_member_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, SkillMember $skillMember): Response
    {
        $form = $this->createForm(SkillMemberType::class, $skillMember);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('skill_member_index');
        }

        return $this->render('skill_member/edit.html.twig', [
            'skill_member' => $skillMember,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="skill_member_delete", methods={"DELETE"})
     */
    public function delete(Request $request, SkillMember $skillMember): Response
    {
        if ($this->isCsrfTokenValid('delete'.$skillMember->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($skillMember);
            $entityManager->flush();
        }

        return $this->redirectToRoute('skill_member_index');
    }
}
