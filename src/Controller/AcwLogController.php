<?php

namespace App\Controller;

use App\Entity\AcwLog;
use App\Form\AcwLogType;
use App\Repository\AcwLogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/acw/log")
 */
class AcwLogController extends AbstractController
{
    /**
     * @Route("/", name="acw_log_index", methods={"GET"})
     */
    public function index(AcwLogRepository $acwLogRepository): Response
    {
        return $this->render('acw_log/index.html.twig', [
            'acw_logs' => $acwLogRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="acw_log_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $acwLog = new AcwLog();
        $form = $this->createForm(AcwLogType::class, $acwLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($acwLog);
            $entityManager->flush();

            return $this->redirectToRoute('acw_log_index');
        }

        return $this->render('acw_log/new.html.twig', [
            'acw_log' => $acwLog,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="acw_log_show", methods={"GET"})
     */
    public function show(AcwLog $acwLog): Response
    {
        return $this->render('acw_log/show.html.twig', [
            'acw_log' => $acwLog,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="acw_log_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, AcwLog $acwLog): Response
    {
        $form = $this->createForm(AcwLogType::class, $acwLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('acw_log_index');
        }

        return $this->render('acw_log/edit.html.twig', [
            'acw_log' => $acwLog,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="acw_log_delete", methods={"DELETE"})
     */
    public function delete(Request $request, AcwLog $acwLog): Response
    {
        if ($this->isCsrfTokenValid('delete'.$acwLog->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($acwLog);
            $entityManager->flush();
        }

        return $this->redirectToRoute('acw_log_index');
    }
}
