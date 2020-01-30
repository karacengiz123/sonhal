<?php

namespace App\Controller;

use App\Entity\EvaluationStatus;
use App\Form\EvaluationStatusType;
use App\Repository\EvaluationStatusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/evaluation/status")
 */
class EvaluationStatusController extends AbstractController
{
    /**
     * @Route("/", name="evaluation_status_index", methods={"GET"})
     */
    public function index(EvaluationStatusRepository $evaluationStatusRepository): Response
    {
        return $this->render('evaluation_status/index.html.twig', [
            'evaluation_statuses' => $evaluationStatusRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="evaluation_status_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $evaluationStatus = new EvaluationStatus();
        $form = $this->createForm(EvaluationStatusType::class, $evaluationStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evaluationStatus);
            $entityManager->flush();

            return $this->redirectToRoute('evaluation_status_index');
        }

        return $this->render('evaluation_status/new.html.twig', [
            'evaluation_status' => $evaluationStatus,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="evaluation_status_show", methods={"GET"})
     */
    public function show(EvaluationStatus $evaluationStatus): Response
    {
        return $this->render('evaluation_status/show.html.twig', [
            'evaluation_status' => $evaluationStatus,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="evaluation_status_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, EvaluationStatus $evaluationStatus): Response
    {
        $form = $this->createForm(EvaluationStatusType::class, $evaluationStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('evaluation_status_index');
        }

        return $this->render('evaluation_status/edit.html.twig', [
            'evaluation_status' => $evaluationStatus,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="evaluation_status_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EvaluationStatus $evaluationStatus): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evaluationStatus->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($evaluationStatus);
            $entityManager->flush();
        }

        return $this->redirectToRoute('evaluation_status_index');
    }
}
