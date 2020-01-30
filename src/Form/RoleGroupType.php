<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Role;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoleGroupType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',EntityType::class, [
                "class" =>Group::class,
                "choice_label" => 'name',
                "label" => "İsim",
                "attr" => ["class"=>"form-control"]
            ])
            ->add('roles',EntityType::class,[
                "class" => Role::class,
                "choice_label" =>'title',
                "label" => "Rol",
                "multiple" => true,
                "expanded" => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('r')
                        ->orderBy('r.title', 'ASC');
                },
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Group::class,
        ]);
    }
}
