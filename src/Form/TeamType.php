<?php

namespace App\Form;

use App\Entity\Team;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class,[
                "label"=> "İsim",
                "attr" => ["class"=>"form-control"]
            ])
            ->add('breakLimit', NumberType::class,[
                "label"=>"Mola Limit",
                "attr"=> ["class"=>"form-control"]
            ])
            ->add('manager',null,[
                "label"=>"Yönetici",
                "attr"=> ["class"=>"form-control"]
            ])
            ->add('managerBackup',null,[
                "label"=>"Yedek Yönetici",
                "attr"=>["class"=>"form-control"]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Team::class,
        ]);
    }
}
