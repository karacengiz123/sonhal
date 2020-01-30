<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Role;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $roles = $options['roles'];
        $builder
            ->add('name',null, [
                "label" => "İsim",
                "attr" => ["class"=>"form-control"]
            ])
            ->add('roles',ChoiceType::class, [
                "choices" => $roles,
                "multiple" => true,
                "expanded" => true,
                "label" => "Rol",
            ])
        ;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Group::class,
            'roles' => '',
        ]);
    }
}
