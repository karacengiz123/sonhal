<?php

namespace App\Controller;

use App\Entity\FormCategory;
use App\Form\FormCategoryType;
use App\Repository\FormCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/form/category")
 */
class FormCategoryController extends AbstractController
{
    /**
     * @Route("/", name="form_category_index", methods={"GET"})
     */
    public function index(FormCategoryRepository $formCategoryRepository): Response
    {
        return $this->render('form_category/index.html.twig', [
            'form_categories' => $formCategoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="form_category_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $formCategory = new FormCategory();
        $form = $this->createForm(FormCategoryType::class, $formCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($formCategory);
            $entityManager->flush();

            return $this->redirectToRoute('form_category_index');
        }

        return $this->render('form_category/new.html.twig', [
            'form_category' => $formCategory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="form_category_show", methods={"GET"})
     */
    public function show(FormCategory $formCategory): Response
    {
        return $this->render('form_category/show.html.twig', [
            'form_category' => $formCategory,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="form_category_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, FormCategory $formCategory): Response
    {
        $form = $this->createForm(FormCategoryType::class, $formCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('form_category_index');
        }

        return $this->render('form_category/edit.html.twig', [
            'form_category' => $formCategory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="form_category_delete", methods={"DELETE"})
     */
    public function delete(Request $request, FormCategory $formCategory): Response
    {
        if ($this->isCsrfTokenValid('delete'.$formCategory->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($formCategory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('form_category_index');
    }
}
