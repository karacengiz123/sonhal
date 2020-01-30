<?php

namespace App\Form;

use App\Entity\SiebelLog;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SiebelLogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('callid')
            ->add('response')
            ->add('request')
            ->add('createdDate')
            ->add('activityId')
            ->add('SRId')
            ->add('ContactId')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SiebelLog::class,
        ]);
    }
}
