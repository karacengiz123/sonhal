<?php

namespace App\Controller;

use App\Entity\FormQuestionOption;
use App\Form\FormQuestionOptionType;
use App\Repository\FormQuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/form/question/option")
 */
class FormQuestionOptionController extends AbstractController
{
    /**
     * @Route("/", name="form_question_option_index", methods={"GET"})
     */
    public function index(FormQuestionRepository $formQuestionRepository): Response
    {
        return $this->render('form_question_option/index.html.twig', [
            'form_question_options' => $formQuestionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="form_question_option_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $formQuestionOption = new FormQuestionOption();
        $form = $this->createForm(FormQuestionOptionType::class, $formQuestionOption);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($formQuestionOption);
            $entityManager->flush();

            return $this->redirectToRoute('form_question_option_index');
        }

        return $this->render('form_question_option/new.html.twig', [
            'form_question_option' => $formQuestionOption,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="form_question_option_show", methods={"GET"})
     */
    public function show(FormQuestionOption $formQuestionOption): Response
    {
        return $this->render('form_question_option/show.html.twig', [
            'form_question_option' => $formQuestionOption,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="form_question_option_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, FormQuestionOption $formQuestionOption): Response
    {
        $form = $this->createForm(FormQuestionOptionType::class, $formQuestionOption);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('form_question_option_index');
        }

        return $this->render('form_question_option/edit.html.twig', [
            'form_question_option' => $formQuestionOption,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="form_question_option_delete", methods={"DELETE"})
     */
    public function delete(Request $request, FormQuestionOption $formQuestionOption): Response
    {
        if ($this->isCsrfTokenValid('delete'.$formQuestionOption->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($formQuestionOption);
            $entityManager->flush();
        }

        return $this->redirectToRoute('form_question_option_index');
    }
}
