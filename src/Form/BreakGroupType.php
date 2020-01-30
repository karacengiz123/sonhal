<?php

namespace App\Form;

use App\Entity\BreakGroup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BreakGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',null,[
                "label"=>"Grup Adı"
            ])
            ->add('maxUser',NumberType::class,[
                "label"=>"Maximum Temsilci"
            ])
            ->add('breakLimit',NumberType::class,[
                "label"=>"Mola Limiti"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BreakGroup::class,
        ]);
    }
}
