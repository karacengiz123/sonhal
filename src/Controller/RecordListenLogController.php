<?php

namespace App\Controller;

use App\Entity\RecordListenLog;
use App\Form\RecordListenLogType;
use App\Repository\RecordListenLogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/record/listen/log")
 */
class RecordListenLogController extends AbstractController
{
    /**
     * @Route("/", name="record_listen_log_index", methods={"GET"})
     */
    public function index(RecordListenLogRepository $recordListenLogRepository): Response
    {
        return $this->render('record_listen_log/index.html.twig', [
            'record_listen_logs' => $recordListenLogRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="record_listen_log_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $recordListenLog = new RecordListenLog();
        $form = $this->createForm(RecordListenLogType::class, $recordListenLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($recordListenLog);
            $entityManager->flush();

            return $this->redirectToRoute('record_listen_log_index');
        }

        return $this->render('record_listen_log/new.html.twig', [
            'record_listen_log' => $recordListenLog,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="record_listen_log_show", methods={"GET"})
     */
    public function show(RecordListenLog $recordListenLog): Response
    {
        return $this->render('record_listen_log/show.html.twig', [
            'record_listen_log' => $recordListenLog,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="record_listen_log_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, RecordListenLog $recordListenLog): Response
    {
        $form = $this->createForm(RecordListenLogType::class, $recordListenLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('record_listen_log_index');
        }

        return $this->render('record_listen_log/edit.html.twig', [
            'record_listen_log' => $recordListenLog,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="record_listen_log_delete", methods={"DELETE"})
     */
    public function delete(Request $request, RecordListenLog $recordListenLog): Response
    {
        if ($this->isCsrfTokenValid('delete'.$recordListenLog->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($recordListenLog);
            $entityManager->flush();
        }

        return $this->redirectToRoute('record_listen_log_index');
    }
}
