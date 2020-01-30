<?php

namespace App\Controller;

use App\Entity\Greeting;
use App\Form\GreetingType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/greeting")
 */
class GreetingController extends AbstractController
{
    /**
     * @Route("/", name="greeting_index", methods={"GET"})
     */
    public function index(): Response
    {
        $greetings = $this->getDoctrine()
            ->getRepository(Greeting::class)
            ->findAll();

        return $this->render('greeting/index.html.twig', [
            'greetings' => $greetings,
        ]);
    }

    /**
     * @Route("/new", name="greeting_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $greeting = new Greeting();
        $form = $this->createForm(GreetingType::class, $greeting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($greeting);
            $entityManager->flush();

            return $this->redirectToRoute('greeting_index');
        }

        return $this->render('greeting/new.html.twig', [
            'greeting' => $greeting,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="greeting_show", methods={"GET"})
     */
    public function show(Greeting $greeting): Response
    {
        return $this->render('greeting/show.html.twig', [
            'greeting' => $greeting,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="greeting_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Greeting $greeting): Response
    {
        $form = $this->createForm(GreetingType::class, $greeting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('greeting_index');
        }

        return $this->render('greeting/edit.html.twig', [
            'greeting' => $greeting,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="greeting_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Greeting $greeting): Response
    {
        if ($this->isCsrfTokenValid('delete'.$greeting->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($greeting);
            $entityManager->flush();
        }

        return $this->redirectToRoute('greeting_index');
    }
}
