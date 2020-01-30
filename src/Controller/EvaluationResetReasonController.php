<?php

namespace App\Controller;

use App\Entity\EvaluationResetReason;
use App\Form\EvaluationResetReasonType;
use App\Repository\EvaluationResetReasonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/evaluation/reset/reason")
 */
class EvaluationResetReasonController extends AbstractController
{
    /**
     * @Route("/", name="evaluation_reset_reason_index", methods={"GET"})
     */
    public function index(EvaluationResetReasonRepository $evaluationResetReasonRepository): Response
    {
        return $this->render('evaluation_reset_reason/index.html.twig', [
            'evaluation_reset_reasons' => $evaluationResetReasonRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="evaluation_reset_reason_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $evaluationResetReason = new EvaluationResetReason();
        $form = $this->createForm(EvaluationResetReasonType::class, $evaluationResetReason);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evaluationResetReason);
            $entityManager->flush();

            return $this->redirectToRoute('evaluation_reset_reason_index');
        }

        return $this->render('evaluation_reset_reason/new.html.twig', [
            'evaluation_reset_reason' => $evaluationResetReason,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="evaluation_reset_reason_show", methods={"GET"})
     */
    public function show(EvaluationResetReason $evaluationResetReason): Response
    {
        return $this->render('evaluation_reset_reason/show.html.twig', [
            'evaluation_reset_reason' => $evaluationResetReason,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="evaluation_reset_reason_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, EvaluationResetReason $evaluationResetReason): Response
    {
        $form = $this->createForm(EvaluationResetReasonType::class, $evaluationResetReason);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('evaluation_reset_reason_index');
        }

        return $this->render('evaluation_reset_reason/edit.html.twig', [
            'evaluation_reset_reason' => $evaluationResetReason,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="evaluation_reset_reason_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EvaluationResetReason $evaluationResetReason): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evaluationResetReason->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($evaluationResetReason);
            $entityManager->flush();
        }

        return $this->redirectToRoute('evaluation_reset_reason_index');
    }
}
