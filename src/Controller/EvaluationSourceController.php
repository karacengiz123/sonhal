<?php

namespace App\Controller;

use App\Entity\EvaluationSource;
use App\Form\EvaluationSourceType;
use App\Repository\EvaluationSourceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/evaluation/source")
 */
class EvaluationSourceController extends AbstractController
{
    /**
     * @Route("/", name="evaluation_source_index", methods={"GET"})
     */
    public function index(EvaluationSourceRepository $evaluationSourceRepository): Response
    {
        return $this->render('evaluation_source/index.html.twig', [
            'evaluation_sources' => $evaluationSourceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="evaluation_source_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $evaluationSource = new EvaluationSource();
        $form = $this->createForm(EvaluationSourceType::class, $evaluationSource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evaluationSource);
            $entityManager->flush();

            return $this->redirectToRoute('evaluation_source_index');
        }

        return $this->render('evaluation_source/new.html.twig', [
            'evaluation_source' => $evaluationSource,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="evaluation_source_show", methods={"GET"})
     */
    public function show(EvaluationSource $evaluationSource): Response
    {
        return $this->render('evaluation_source/show.html.twig', [
            'evaluation_source' => $evaluationSource,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="evaluation_source_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, EvaluationSource $evaluationSource): Response
    {
        $form = $this->createForm(EvaluationSourceType::class, $evaluationSource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('evaluation_source_index');
        }

        return $this->render('evaluation_source/edit.html.twig', [
            'evaluation_source' => $evaluationSource,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="evaluation_source_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EvaluationSource $evaluationSource): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evaluationSource->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($evaluationSource);
            $entityManager->flush();
        }

        return $this->redirectToRoute('evaluation_source_index');
    }
}
