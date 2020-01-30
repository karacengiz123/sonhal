<?php

namespace App\Controller;

use App\Entity\IvrServiceLog;
use App\Form\IvrServiceLogType;
use App\Repository\IvrServiceLogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ivr/service/log")
 */
class IvrServiceLogController extends AbstractController
{
    /**
     * @Route("/", name="ivr_service_log_index", methods={"GET"})
     */
    public function index(IvrServiceLogRepository $ivrServiceLogRepository): Response
    {
        return $this->render('ivr_service_log/index.html.twig', [
            'ivr_service_logs' => $ivrServiceLogRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="ivr_service_log_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $ivrServiceLog = new IvrServiceLog();
        $form = $this->createForm(IvrServiceLogType::class, $ivrServiceLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ivrServiceLog);
            $entityManager->flush();

            return $this->redirectToRoute('ivr_service_log_index');
        }

        return $this->render('ivr_service_log/new.html.twig', [
            'ivr_service_log' => $ivrServiceLog,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ivr_service_log_show", methods={"GET"})
     */
    public function show(IvrServiceLog $ivrServiceLog): Response
    {
        return $this->render('ivr_service_log/show.html.twig', [
            'ivr_service_log' => $ivrServiceLog,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="ivr_service_log_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, IvrServiceLog $ivrServiceLog): Response
    {
        $form = $this->createForm(IvrServiceLogType::class, $ivrServiceLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('ivr_service_log_index');
        }

        return $this->render('ivr_service_log/edit.html.twig', [
            'ivr_service_log' => $ivrServiceLog,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ivr_service_log_delete", methods={"DELETE"})
     */
    public function delete(Request $request, IvrServiceLog $ivrServiceLog): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ivrServiceLog->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($ivrServiceLog);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ivr_service_log_index');
    }
}
