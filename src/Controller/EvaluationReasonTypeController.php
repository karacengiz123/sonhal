<?php

namespace App\Controller;

use App\Entity\EvaluationReasonType;
use App\Form\EvaluationReasonTypeType;
use App\Repository\EvaluationReasonTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/evaluation/reason/type")
 */
class EvaluationReasonTypeController extends AbstractController
{
    /**
     * @Route("/", name="evaluation_reason_type_index", methods={"GET"})
     */
    public function index(EvaluationReasonTypeRepository $evaluationReasonTypeRepository): Response
    {
        return $this->render('evaluation_reason_type/index.html.twig', [
            'evaluation_reason_types' => $evaluationReasonTypeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="evaluation_reason_type_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $evaluationReasonType = new EvaluationReasonType();
        $form = $this->createForm(EvaluationReasonTypeType::class, $evaluationReasonType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evaluationReasonType);
            $entityManager->flush();

            return $this->redirectToRoute('evaluation_reason_type_index');
        }

        return $this->render('evaluation_reason_type/new.html.twig', [
            'evaluation_reason_type' => $evaluationReasonType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="evaluation_reason_type_show", methods={"GET"})
     */
    public function show(EvaluationReasonType $evaluationReasonType): Response
    {
        return $this->render('evaluation_reason_type/show.html.twig', [
            'evaluation_reason_type' => $evaluationReasonType,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="evaluation_reason_type_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, EvaluationReasonType $evaluationReasonType): Response
    {
        $form = $this->createForm(EvaluationReasonTypeType::class, $evaluationReasonType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('evaluation_reason_type_index');
        }

        return $this->render('evaluation_reason_type/edit.html.twig', [
            'evaluation_reason_type' => $evaluationReasonType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="evaluation_reason_type_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EvaluationReasonType $evaluationReasonType): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evaluationReasonType->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($evaluationReasonType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('evaluation_reason_type_index');
    }
}
