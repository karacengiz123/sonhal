<?php

namespace App\Controller;

use App\Entity\QueuesMembersDynamic;
use App\Form\QueuesMembersDynamicType;
use App\Repository\QueuesMembersDynamicRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/queues/members/dynamic")
 */
class QueuesMembersDynamicController extends AbstractController
{
    /**
     * @Route("/", name="queues_members_dynamic_index", methods={"GET"})
     */
    public function index(QueuesMembersDynamicRepository $queuesMembersDynamicRepository): Response
    {
        return $this->render('queues_members_dynamic/index.html.twig', [
            'queues_members_dynamics' => $queuesMembersDynamicRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="queues_members_dynamic_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $queuesMembersDynamic = new QueuesMembersDynamic();
        $form = $this->createForm(QueuesMembersDynamicType::class, $queuesMembersDynamic);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($queuesMembersDynamic);
            $entityManager->flush();

            return $this->redirectToRoute('queues_members_dynamic_index');
        }

        return $this->render('queues_members_dynamic/new.html.twig', [
            'queues_members_dynamic' => $queuesMembersDynamic,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="queues_members_dynamic_show", methods={"GET"})
     */
    public function show(QueuesMembersDynamic $queuesMembersDynamic): Response
    {
        return $this->render('queues_members_dynamic/show.html.twig', [
            'queues_members_dynamic' => $queuesMembersDynamic,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="queues_members_dynamic_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, QueuesMembersDynamic $queuesMembersDynamic): Response
    {
        $form = $this->createForm(QueuesMembersDynamicType::class, $queuesMembersDynamic);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('queues_members_dynamic_index');
        }

        return $this->render('queues_members_dynamic/edit.html.twig', [
            'queues_members_dynamic' => $queuesMembersDynamic,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="queues_members_dynamic_delete", methods={"DELETE"})
     */
    public function delete(Request $request, QueuesMembersDynamic $queuesMembersDynamic): Response
    {
        if ($this->isCsrfTokenValid('delete'.$queuesMembersDynamic->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($queuesMembersDynamic);
            $entityManager->flush();
        }

        return $this->redirectToRoute('queues_members_dynamic_index');
    }
}
