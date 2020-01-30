<?php

namespace App\Controller;

use App\Entity\SiebelLog;
use App\Form\SiebelLogType;
use App\Repository\SiebelLogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/siebel/log")
 */
class SiebelLogController extends AbstractController
{
    /**
     * @Route("/", name="siebel_log_index", methods={"GET"})
     */
    public function index(SiebelLogRepository $siebelLogRepository): Response
    {
        return $this->render('siebel_log/index.html.twig', [
            'siebel_logs' => $siebelLogRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="siebel_log_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $siebelLog = new SiebelLog();
        $form = $this->createForm(SiebelLogType::class, $siebelLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($siebelLog);
            $entityManager->flush();

            return $this->redirectToRoute('siebel_log_index');
        }

        return $this->render('siebel_log/new.html.twig', [
            'siebel_log' => $siebelLog,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="siebel_log_show", methods={"GET"})
     */
    public function show(SiebelLog $siebelLog): Response
    {
        return $this->render('siebel_log/show.html.twig', [
            'siebel_log' => $siebelLog,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="siebel_log_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, SiebelLog $siebelLog): Response
    {
        $form = $this->createForm(SiebelLogType::class, $siebelLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('siebel_log_index');
        }

        return $this->render('siebel_log/edit.html.twig', [
            'siebel_log' => $siebelLog,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="siebel_log_delete", methods={"DELETE"})
     */
    public function delete(Request $request, SiebelLog $siebelLog): Response
    {
        if ($this->isCsrfTokenValid('delete'.$siebelLog->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($siebelLog);
            $entityManager->flush();
        }

        return $this->redirectToRoute('siebel_log_index');
    }
}
