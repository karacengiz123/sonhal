<?php

namespace App\UserBundle\Controller;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/users", name="index_users")
     * @return Response
     */
    public function indexUsersAction()
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository(User::class)->findAll();
        return $this->render('@User/users/index.html.twig', array(
            "users" => $users
        ));
    }

    /**
     * @Route("/users/edit/save")
     * @param Request $request
     * @return Response
     */
    public function indexUsersEditSaveAction(Request $request)
    {
            $em = $this->getDoctrine()->getManager();
            $users = $em->getRepository(User::class);
            $user = $users->find($request->get('id'));

            if ($request->get('password') == ""){
                $password = $user->getPassword();
            }else{
                $password = $request->get('password');
            }

            $user
                ->setUsername($request->get('username'))
                ->setEmail($request->get('email'))
                ->setPassword($password)
                ->setUsernameCanonical($request->get('username'))
                ->setEmailCanonical($request->get('email'));

            $em->persist($user);
            $em->flush();

            header("Refresh: 1; url=/users");
            return new Response("Kayıt Başarılı");
    }

    /**
     * @Route("/users/edit/{id}")
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function indexUsersEditAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository(User::class);
        $user = $users->find($id);
        $username = $user->getUsername();
        $email = $user->getEmail();

        return $this->render('@User/users/users_edit.html.twig', array(
            "username" => $username,
            "email" => $email,
            "id" => $id
        ));
    }

    /**
     * @Route("/users/create")
     * @param Request $request
     * @return Response
     */
    public function indexUsersCreateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = new User();

        $user
            ->setUsername($request->get('username'))
            ->setEmail($request->get('email'))
            ->setPassword($request->get('password'))
            ->setUsernameCanonical($request->get('username'))
            ->setEmailCanonical($request->get('email'));

        $em->persist($user);
        $em->flush();

        header("Refresh: 1; url=/users");
        return new Response("Kayıt Başarılı");
    }

}