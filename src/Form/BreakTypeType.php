<?php

namespace App\Form;

use App\Entity\BreakType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BreakTypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null,[
                'label'=>'Adı',
                'attr' => ['class' => 'aaaaaa']

            ])
            ->add('addeableRole', null,[
                'label'=>'Rol Ekle'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BreakType::class,
        ]);
    }
}
