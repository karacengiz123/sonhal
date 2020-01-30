<?php

namespace App\Controller;

use App\Entity\AcwType;
use App\Entity\Group;
use App\Form\AcwTypeType;
use App\Repository\AcwTypeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**

 * @Route("/acw/type")
 */
class AcwTypeController extends AbstractController
{
    /**
     * @IsGranted("ROLE_ACW_TYPE_INDEX")
     * @Route("/", name="acw_type_index", methods={"GET"})
     */
    public function index(AcwTypeRepository $acwTypeRepository): Response
    {
        return $this->render('acw_type/index.html.twig', [
            'acw_types' => $acwTypeRepository->findAll(),
        ]);
    }

    /**
     * @IsGranted("ROLE_ACW_TYPE_NEW")
     * @Route("/new", name="acw_type_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $acwType = new AcwType();
        $roleGroups = $this->getDoctrine()->getRepository(Group::class)->findAll();
        $roles = [];
        foreach ($roleGroups as $roleGroup){
            $roles[$roleGroup->getName()]=$roleGroup->getName();
        }
        $builder = $this->createFormBuilder($acwType);
        $builder
            ->add('name',null,[
                "label"=>"İşlem Adı"
            ])
            ->add('role',ChoiceType::class,[
                'choices' => $roles
            ])
        ;
        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($acwType);
            $entityManager->flush();

            return $this->redirectToRoute('acw_type_index');
        }

        return $this->render('acw_type/new.html.twig', [
            'acw_type' => $acwType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_ACW_TYPE_SHOW")
     * @Route("/{id}", name="acw_type_show", methods={"GET"})
     */
    public function show(AcwType $acwType): Response
    {
        return $this->render('acw_type/show.html.twig', [
            'acw_type' => $acwType,
        ]);
    }

    /**
     * @IsGranted("ROLE_ACW_TYPE_EDIT")
     * @Route("/{id}/edit", name="acw_type_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, AcwType $acwType): Response
    {
        if (in_array($acwType->getId(),[1,2,3])){
            return $this->redirectToRoute('acw_type_index');
        }else{
            $roleGroups = $this->getDoctrine()->getRepository(Group::class)->findAll();
            $roles = [];
            foreach ($roleGroups as $roleGroup){
                $roles[$roleGroup->getName()]=$roleGroup->getName();
            }
            $builder = $this->createFormBuilder($acwType);
            $builder
                ->add('name',null,[
                    "label"=>"İşlem Adı"
                ])
                ->add('role',ChoiceType::class,[
                    'choices' => $roles
                ])
            ;
            $form = $builder->getForm();
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('acw_type_index', [
                    'id' => $acwType->getid(),
                ]);
            }

            return $this->render('acw_type/edit.html.twig', [
                'acw_type' => $acwType,
                'form' => $form->createView(),
            ]);
        }
    }

    /**
     * @IsGranted("ROLE_ACW_TYPE_DELETE")
     * @Route("/{id}", name="acw_type_delete", methods={"DELETE"})
     */
    public function delete(Request $request, AcwType $acwType): Response
    {
        if (in_array($acwType->getId(),[1,2,3])){
            return $this->redirectToRoute('acw_type_index');
        }else {
            if ($this->isCsrfTokenValid('delete' . $acwType->getid(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($acwType);
                $entityManager->flush();
            }

            return $this->redirectToRoute('acw_type_index');
        }
    }
}
