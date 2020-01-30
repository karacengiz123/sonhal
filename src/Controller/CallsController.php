<?php

namespace App\Controller;

use App\Entity\Calls;
use App\Form\CallsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/calls")
 */
class CallsController extends AbstractController
{
    /**
     * @Route("/", name="calls_index", methods={"GET"})
     */
    public function index(): Response
    {
        $calls = $this->getDoctrine()
            ->getRepository(Calls::class)
            ->findAll();

        return $this->render('calls/index.html.twig', [
            'calls' => $calls,
        ]);
    }

    /**
     * @Route("/new", name="calls_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $call = new Calls();
        $form = $this->createForm(CallsType::class, $call);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($call);
            $entityManager->flush();

            return $this->redirectToRoute('calls_index');
        }

        return $this->render('calls/new.html.twig', [
            'call' => $call,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idx}", name="calls_show", methods={"GET"})
     */
    public function show(Calls $call): Response
    {
        return $this->render('calls/show.html.twig', [
            'call' => $call,
        ]);
    }

    /**
     * @Route("/{idx}/edit", name="calls_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Calls $call): Response
    {
        $form = $this->createForm(CallsType::class, $call);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('calls_index');
        }

        return $this->render('calls/edit.html.twig', [
            'call' => $call,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idx}", name="calls_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Calls $call): Response
    {
        if ($this->isCsrfTokenValid('delete'.$call->getIdx(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($call);
            $entityManager->flush();
        }

        return $this->redirectToRoute('calls_index');
    }
}
