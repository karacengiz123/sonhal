<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Role;
use App\Form\Group1Type;
use App\Form\GroupType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/group")
 */
class GroupController extends AbstractController
{
    /**
     * @IsGranted("ROLE_GROUP_INDEX")
     * @Route("/", name="group_index", methods={"GET"})
     */
    public function index(): Response
    {
        $groups = $this->getDoctrine()
            ->getRepository(Group::class)
            ->findAll();

        return $this->render('group/index.html.twig', [
            'groups' => $groups,
        ]);
    }

    /**
     * @IsGranted("ROLE_GROUP_NEW")
     * @Route("/new", name="group_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $group = new Group('');
        $roleArray = $this->getDoctrine()->getRepository(Role::class)->createQueryBuilder('r')
            ->getQuery()->getArrayResult();

        $roles = array_column($roleArray,"id","title");

        $form = $this->createForm(GroupType::class, $group,['roles' => $roles]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $formData = $request->request->get("group");

            $group->setRoles($formData['roles']);
            $entityManager->persist($group);
            $entityManager->flush();

            return $this->redirectToRoute('group_index');
        }


        return $this->render('group/new.html.twig', [
            'group' => $group,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_GROUP_SHOW")
     * @Route("/{id}", name="group_show", methods={"GET"})
     */
    public function show(Group $group): Response
    {
        return $this->render('group/show.html.twig', [
            'group' => $group,
        ]);
    }

    /**
     * @IsGranted("ROLE_GROUP_EDIT")
     * @Route("/{id}/edit", name="group_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Group $group): Response
    {

        $roleArray = $this->getDoctrine()->getRepository(Role::class)->createQueryBuilder('r')
            ->getQuery()->getArrayResult();

        $roles = array_column($roleArray,"id","title");
        $form = $this->createForm(GroupType::class, $group,['roles' => $roles]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $formData = $request->request->get("group");

            $group->setRoles($formData['roles']);
            $entityManager->persist($group);
            $entityManager->flush();

            return $this->redirectToRoute('group_index');
        }

        return $this->render('group/edit.html.twig', [
            'group' => $group,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_GROUP_DELETE")
     * @Route("/{id}", name="group_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Group $group): Response
    {
        if ($this->isCsrfTokenValid('delete'.$group->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($group);
            $entityManager->flush();
        }

        return $this->redirectToRoute('group_index');
    }
}
