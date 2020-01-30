<?php

namespace App\Form;

use App\Entity\QueuesMembersDynamic;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QueuesMembersDynamicType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('queue')
            ->add('member')
            ->add('penalty')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => QueuesMembersDynamic::class,
        ]);
    }
}
