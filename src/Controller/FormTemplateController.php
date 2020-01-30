<?php

namespace App\Controller;

use App\Entity\FormTemplate;
use App\Form\FormTemplateType;
use App\Repository\FormTemplateRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/form/template")
 */
class FormTemplateController extends AbstractController
{
    /**
     * @Route("/", name="form_template_index", methods={"GET"})
     */
    public function index(FormTemplateRepository $formTemplateRepository): Response
    {
        return $this->render('form_template/index.html.twig', [
            'form_templates' => $formTemplateRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="form_template_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $formTemplate = new FormTemplate();
        $form = $this->createForm(FormTemplateType::class, $formTemplate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($formTemplate);
            $entityManager->flush();

            return $this->redirectToRoute('form_template_index');
        }

        return $this->render('form_template/new.html.twig', [
            'form_template' => $formTemplate,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="form_template_show", methods={"GET"})
     */
    public function show(FormTemplate $formTemplate): Response
    {
        return $this->render('form_template/show.html.twig', [
            'form_template' => $formTemplate,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="form_template_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, FormTemplate $formTemplate): Response
    {
        $form = $this->createForm(FormTemplateType::class, $formTemplate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('form_template_index');
        }

        return $this->render('form_template/edit.html.twig', [
            'form_template' => $formTemplate,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="form_template_delete", methods={"DELETE"})
     */
    public function delete(Request $request, FormTemplate $formTemplate): Response
    {
        if ($this->isCsrfTokenValid('delete'.$formTemplate->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($formTemplate);
            $entityManager->flush();
        }

        return $this->redirectToRoute('form_template_index');
    }
}
