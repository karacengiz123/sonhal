<?php

namespace App\Controller;

use App\Entity\Ci2ci;
use App\Form\Ci2ciType;
use App\Repository\Ci2ciRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ci2ci")
 */
class Ci2ciController extends AbstractController
{
    /**
     * @Route("/", name="ci2ci_index", methods={"GET"})
     */
    public function index(Ci2ciRepository $ci2ciRepository): Response
    {
        return $this->render('ci2ci/index.html.twig', [
            'ci2cis' => $ci2ciRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="ci2ci_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $ci2ci = new Ci2ci();
        $form = $this->createForm(Ci2ciType::class, $ci2ci);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ci2ci);
            $entityManager->flush();

            return $this->redirectToRoute('ci2ci_index');
        }

        return $this->render('ci2ci/new.html.twig', [
            'ci2ci' => $ci2ci,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ci2ci_show", methods={"GET"})
     */
    public function show(Ci2ci $ci2ci): Response
    {
        return $this->render('ci2ci/show.html.twig', [
            'ci2ci' => $ci2ci,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="ci2ci_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Ci2ci $ci2ci): Response
    {
        $form = $this->createForm(Ci2ciType::class, $ci2ci);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('ci2ci_index');
        }

        return $this->render('ci2ci/edit.html.twig', [
            'ci2ci' => $ci2ci,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ci2ci_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Ci2ci $ci2ci): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ci2ci->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($ci2ci);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ci2ci_index');
    }
}
