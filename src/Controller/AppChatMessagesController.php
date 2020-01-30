<?php

namespace App\Controller;

use App\Entity\AppChatMessages;
use App\Form\AppChatMessagesType;
use App\Repository\AppChatMessagesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/app/chat/messages")
 */
class AppChatMessagesController extends AbstractController
{
    /**
     * @Route("/", name="app_chat_messages_index", methods={"GET"})
     */
    public function index(AppChatMessagesRepository $appChatMessagesRepository): Response
    {
        return $this->render('app_chat_messages/index.html.twig', [
            'app_chat_messages' => $appChatMessagesRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_chat_messages_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $appChatMessage = new AppChatMessages();
        $form = $this->createForm(AppChatMessagesType::class, $appChatMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($appChatMessage);
            $entityManager->flush();

            return $this->redirectToRoute('app_chat_messages_index');
        }

        return $this->render('app_chat_messages/new.html.twig', [
            'app_chat_message' => $appChatMessage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_chat_messages_show", methods={"GET"})
     */
    public function show(AppChatMessages $appChatMessage): Response
    {
        return $this->render('app_chat_messages/show.html.twig', [
            'app_chat_message' => $appChatMessage,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_chat_messages_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, AppChatMessages $appChatMessage): Response
    {
        $form = $this->createForm(AppChatMessagesType::class, $appChatMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_chat_messages_index');
        }

        return $this->render('app_chat_messages/edit.html.twig', [
            'app_chat_message' => $appChatMessage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_chat_messages_delete", methods={"DELETE"})
     */
    public function delete(Request $request, AppChatMessages $appChatMessage): Response
    {
        if ($this->isCsrfTokenValid('delete'.$appChatMessage->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($appChatMessage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_chat_messages_index');
    }
}
