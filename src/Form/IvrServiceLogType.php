<?php

namespace App\Form;

use App\Entity\IvrServiceLog;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IvrServiceLogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('callId')
            ->add('alias')
            ->add('input')
            ->add('request')
            ->add('response')
            ->add('createsAt')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => IvrServiceLog::class,
        ]);
    }
}
