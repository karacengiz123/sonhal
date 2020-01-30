<?php

namespace App\Controller;

use App\Entity\EvaluationExtraSource;
use App\Form\EvaluationExtraSourceType;
use App\Repository\EvaluationExtraSourceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/evaluation/extra/source")
 */
class EvaluationExtraSourceController extends AbstractController
{
    /**
     * @Route("/", name="evaluation_extra_source_index", methods={"GET"})
     */
    public function index(EvaluationExtraSourceRepository $evaluationExtraSourceRepository): Response
    {
        return $this->render('evaluation_extra_source/index.html.twig', [
            'evaluation_extra_sources' => $evaluationExtraSourceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="evaluation_extra_source_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $evaluationExtraSource = new EvaluationExtraSource();
        $form = $this->createForm(EvaluationExtraSourceType::class, $evaluationExtraSource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evaluationExtraSource);
            $entityManager->flush();

            return $this->redirectToRoute('evaluation_extra_source_index');
        }

        return $this->render('evaluation_extra_source/new.html.twig', [
            'evaluation_extra_source' => $evaluationExtraSource,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="evaluation_extra_source_show", methods={"GET"})
     */
    public function show(EvaluationExtraSource $evaluationExtraSource): Response
    {
        return $this->render('evaluation_extra_source/show.html.twig', [
            'evaluation_extra_source' => $evaluationExtraSource,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="evaluation_extra_source_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, EvaluationExtraSource $evaluationExtraSource): Response
    {
        $form = $this->createForm(EvaluationExtraSourceType::class, $evaluationExtraSource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('evaluation_extra_source_index');
        }

        return $this->render('evaluation_extra_source/edit.html.twig', [
            'evaluation_extra_source' => $evaluationExtraSource,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="evaluation_extra_source_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EvaluationExtraSource $evaluationExtraSource): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evaluationExtraSource->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($evaluationExtraSource);
            $entityManager->flush();
        }

        return $this->redirectToRoute('evaluation_extra_source_index');
    }
}
