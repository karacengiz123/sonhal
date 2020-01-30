<?php

namespace App\Controller;

use App\Entity\FormSection;
use App\Form\FormSectionType;
use App\Repository\FormSectionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/form/section")
 */
class FormSectionController extends AbstractController
{
    /**
     * @Route("/", name="form_section_index", methods={"GET"})
     */
    public function index(FormSectionRepository $formSectionRepository): Response
    {
        return $this->render('form_section/index.html.twig', [
            'form_sections' => $formSectionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="form_section_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $formSection = new FormSection();
        $form = $this->createForm(FormSectionType::class, $formSection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($formSection);
            $entityManager->flush();

            return $this->redirectToRoute('form_section_index');
        }

        return $this->render('form_section/new.html.twig', [
            'form_section' => $formSection,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="form_section_show", methods={"GET"})
     */
    public function show(FormSection $formSection): Response
    {
        return $this->render('form_section/show.html.twig', [
            'form_section' => $formSection,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="form_section_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, FormSection $formSection): Response
    {
        $form = $this->createForm(FormSectionType::class, $formSection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('form_section_index');
        }

        return $this->render('form_section/edit.html.twig', [
            'form_section' => $formSection,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="form_section_delete", methods={"DELETE"})
     */
    public function delete(Request $request, FormSection $formSection): Response
    {
        if ($this->isCsrfTokenValid('delete'.$formSection->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($formSection);
            $entityManager->flush();
        }

        return $this->redirectToRoute('form_section_index');
    }
}
