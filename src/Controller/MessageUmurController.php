<?php

namespace App\Controller;

use App\Entity\WallMessages;
use App\Form\WallMessagesType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageUmurController extends AbstractController
{
    /**
 * @Route(methods={"GET","POST"})
 */

    public function index(Request $request):Response
    {
        $wallMessage = new WallMessages();
        $form = $this->createForm(WallMessagesType::class, $wallMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($wallMessage);
            $entityManager->flush();

            return $this->redirectToRoute('wall_messages_show');
        }

        return $this->render('layout\base.html.twig', [
            'wall_message' => $wallMessage,
            'form' => $form->createView(),
        ]);
    }
}
