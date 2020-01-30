<?php

namespace App\Controller;

use App\Entity\HoldLog;
use App\Entity\RegisterLog;
use App\Form\RegisterLogType;
use App\Helpers\Date;
use App\Repository\RegisterLogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/register/log")
 */
class RegisterLogController extends AbstractController
{

//    /**
//     * @Route("/register-log-start", name="register_log_start")
//     * @param UserInterface $user
//     * @return \Symfony\Component\HttpFoundation\JsonResponse
//     * @throws \Exception
//     */
//    public function registerLogStart(UserInterface $user)
//    {
//        $em=$this->getDoctrine()->getManager();
//
//        $registerLog = new RegisterLog();
//        $registerLog
//            ->setUser($user)
//            ->setStartTime(new \DateTime("Europe/Istanbul"))
//            ->setDuration(0);
//
//        $em->persist($registerLog);
//        $em->flush();
//
//        return $this->json(["return"=>"Kayıt Başarılı"]);
//    }
//
//    /**
//     * @Route("/register-log-stop", name="register_log_stop")
//     * @param UserInterface $user
//     * @param Request $request
//     * @return \Symfony\Component\HttpFoundation\JsonResponse
//     * @throws \Exception
//     */
//    public function registerLogStop(UserInterface $user)
//    {
//        $em=$this->getDoctrine()->getManager();
//        $nowTime = new \DateTime();
//        $registerLogs = $em->getRepository(RegisterLog::class)->findBy(["user"=>$user,"endTime"=>null]);
//        foreach ($registerLogs as $registerLog){
//            $duration = $nowTime->getTimestamp() - $registerLog->getStartTime()->getTimestamp();
//            $registerLog
//                ->setEndTime($nowTime)
//                ->setDuration($duration);
//            $em->persist($registerLog);
//            $em->flush();
//        }
//        return $this->json(["return"=>"Kayıt Başarılı"]);
//    }






    /**
     * @Route("/", name="register_log_index", methods={"GET"})
     */
    public function index(RegisterLogRepository $registerRepository): Response
    {
        return $this->render('register_log/index.html.twig', [
            'register_logs' => $registerRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="register_log_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $registerLog = new RegisterLog();
        $form = $this->createForm(RegisterLogType::class, $registerLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($registerLog);
            $entityManager->flush();

            return $this->redirectToRoute('register_log_index');
        }

        return $this->render('register_log/new.html.twig', [
            'register_log' => $registerLog,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="register_log_show", methods={"GET"})
     */
    public function show(RegisterLog $registerLog): Response
    {
        return $this->render('register_log/show.html.twig', [
            'register_log' => $registerLog,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="register_log_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, RegisterLog $registerLog): Response
    {
        $form = $this->createForm(RegisterLogType::class, $registerLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('register_log_index');
        }

        return $this->render('register_log/edit.html.twig', [
            'register_log' => $registerLog,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="register_log_delete", methods={"DELETE"})
     */
    public function delete(Request $request, RegisterLog $registerLog): Response
    {
        if ($this->isCsrfTokenValid('delete'.$registerLog->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($registerLog);
            $entityManager->flush();
        }

        return $this->redirectToRoute('register_log_index');
    }
}
