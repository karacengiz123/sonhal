<?php

namespace App\Controller;

use App\Entity\RealtimeQueues;
use App\Form\RealtimeQueuesType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/realtime/queues")
 */
class RealtimeQueuesController extends AbstractController
{
    /**
     * @Route("/", name="realtime_queues_index", methods={"GET"})
     */
    public function index(): Response
    {
        $realtimeQueues = $this->getDoctrine()
            ->getRepository(RealtimeQueues::class)
            ->findAll();

        return $this->render('realtime_queues/index.html.twig', [
            'realtime_queues' => $realtimeQueues,
        ]);
    }

    /**
     * @Route("/new", name="realtime_queues_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $realtimeQueue = new RealtimeQueues();
        $form = $this->createForm(RealtimeQueuesType::class, $realtimeQueue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($realtimeQueue);
            $entityManager->flush();

            return $this->redirectToRoute('realtime_queues_index');
        }

        return $this->render('realtime_queues/new.html.twig', [
            'realtime_queue' => $realtimeQueue,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{name}", name="realtime_queues_show", methods={"GET"})
     */
    public function show(RealtimeQueues $realtimeQueue): Response
    {
        return $this->render('realtime_queues/show.html.twig', [
            'realtime_queue' => $realtimeQueue,
        ]);
    }

    /**
     * @Route("/{name}/edit", name="realtime_queues_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, RealtimeQueues $realtimeQueue): Response
    {
        $form = $this->createForm(RealtimeQueuesType::class, $realtimeQueue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('realtime_queues_index');
        }

        return $this->render('realtime_queues/edit.html.twig', [
            'realtime_queue' => $realtimeQueue,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{name}", name="realtime_queues_delete", methods={"DELETE"})
     */
    public function delete(Request $request, RealtimeQueues $realtimeQueue): Response
    {
        if ($this->isCsrfTokenValid('delete'.$realtimeQueue->getName(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($realtimeQueue);
            $entityManager->flush();
        }

        return $this->redirectToRoute('realtime_queues_index');
    }
}
