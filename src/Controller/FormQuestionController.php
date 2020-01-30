<?php

namespace App\Controller;

use App\Entity\FormQuestion;
use App\Form\FormQuestion1Type;
use App\Repository\FormQuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/form/question")
 */
class FormQuestionController extends AbstractController
{
    /**
     * @Route("/", name="form_question_index", methods={"GET"})
     */
    public function index(FormQuestionRepository $formQuestionRepository): Response
    {
        return $this->render('form_question/index.html.twig', [
            'form_questions' => $formQuestionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="form_question_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $formQuestion = new FormQuestion();
        $form = $this->createForm(FormQuestion1Type::class, $formQuestion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($formQuestion);
            $entityManager->flush();

            return $this->redirectToRoute('form_question_index');
        }

        return $this->render('form_question/new.html.twig', [
            'form_question' => $formQuestion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="form_question_show", methods={"GET"})
     */
    public function show(FormQuestion $formQuestion): Response
    {
        return $this->render('form_question/show.html.twig', [
            'form_question' => $formQuestion,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="form_question_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, FormQuestion $formQuestion): Response
    {
        $form = $this->createForm(FormQuestion1Type::class, $formQuestion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('form_question_index');
        }

        return $this->render('form_question/edit.html.twig', [
            'form_question' => $formQuestion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="form_question_delete", methods={"DELETE"})
     */
    public function delete(Request $request, FormQuestion $formQuestion): Response
    {
        if ($this->isCsrfTokenValid('delete'.$formQuestion->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($formQuestion);
            $entityManager->flush();
        }

        return $this->redirectToRoute('form_question_index');
    }
}
