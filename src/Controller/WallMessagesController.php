<?php

namespace App\Controller;

use App\Entity\WallMessages;
use App\Form\WallMessagesType;
use App\Repository\WallMessagesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/wall/messages")
 */
class WallMessagesController extends AbstractController
{

    /**
     * @Route("/add/message", name="add_message", methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function addMessage(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $message = $request->get("message");
        $addMessage = $em->getRepository(WallMessages::class)->findAll();
        if (count($addMessage) == 0){
            $addMessage = new WallMessages();
            $addMessage->setWallmessages($message);
            $em->persist($addMessage);
        }else{
            $addMessage[0]->setWallmessages($message);
            $em->persist($addMessage[0]);
        }
        $em->flush();
        return $this->json(["success"=>true]);
    }

    /**
     * @Route("/old/message", name="old_message")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function oldMessage()
    {
        $em = $this->getDoctrine()->getManager();
        $oldMessage = $em->getRepository(WallMessages::class)->findAll();
        if (count($oldMessage) == 0){
            $oldMessage = "";
        }else{
            $oldMessage = $oldMessage[0]->getWallmessages();
        }
        return $this->json(["oldMessage"=>$oldMessage]);
    }






















    /**
     * @Route("/", name="wall_messages_index", methods={"GET"})
     */
    public function index(WallMessagesRepository $wallMessagesRepository): Response
    {
        return $this->render('wall_messages/index.html.twig', [
            'wall_messages' => $wallMessagesRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="wall_messages_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $wallMessage = new WallMessages();
        $form = $this->createForm(WallMessagesType::class, $wallMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($wallMessage);
            $entityManager->flush();

            return $this->redirectToRoute('wall_messages_new');
        }

        return $this->render('wall_messages/new.html.twig', [
            'wall_message' => $wallMessage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="wall_messages_show", methods={"GET"})
     */
    public function show(WallMessages $wallMessage): Response
    {
        return $this->render('wall_messages/show.html.twig', [
            'wall_message' => $wallMessage,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="wall_messages_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, WallMessages $wallMessage): Response
    {
        $form = $this->createForm(WallMessagesType::class, $wallMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect('/wallboard');
        }

        return $this->render('wall_messages/edit.html.twig', [
            'wall_message' => $wallMessage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="wall_messages_delete", methods={"DELETE"})
     */
    public function delete(Request $request, WallMessages $wallMessage): Response
    {
        if ($this->isCsrfTokenValid('delete'.$wallMessage->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($wallMessage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('wall_messages_index');
    }
}
