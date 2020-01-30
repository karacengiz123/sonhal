<?php

namespace App\Controller;

use App\Entity\EvaluationAnswer;
use App\Form\EvaluationAnswerType;
use App\Repository\EvaluationAnswerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/evaluation/answer")
 */
class EvaluationAnswerController extends AbstractController
{
    /**
     * @Route("/", name="evaluation_answer_index", methods={"GET"})
     */
    public function index(EvaluationAnswerRepository $evaluationAnswerRepository): Response
    {
        return $this->render('evaluation_answer/index.html.twig', [
            'evaluation_answers' => $evaluationAnswerRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="evaluation_answer_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $evaluationAnswer = new EvaluationAnswer();
        $form = $this->createForm(EvaluationAnswerType::class, $evaluationAnswer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evaluationAnswer);
            $entityManager->flush();

            return $this->redirectToRoute('evaluation_answer_index');
        }

        return $this->render('evaluation_answer/new.html.twig', [
            'evaluation_answer' => $evaluationAnswer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="evaluation_answer_show", methods={"GET"})
     */
    public function show(EvaluationAnswer $evaluationAnswer): Response
    {
        return $this->render('evaluation_answer/show.html.twig', [
            'evaluation_answer' => $evaluationAnswer,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="evaluation_answer_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, EvaluationAnswer $evaluationAnswer): Response
    {
        $form = $this->createForm(EvaluationAnswerType::class, $evaluationAnswer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('evaluation_answer_index');
        }

        return $this->render('evaluation_answer/edit.html.twig', [
            'evaluation_answer' => $evaluationAnswer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="evaluation_answer_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EvaluationAnswer $evaluationAnswer): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evaluationAnswer->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($evaluationAnswer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('evaluation_answer_index');
    }
}
