<?php

namespace App\Form;

use App\Entity\Calls;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CallsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('callType')
            ->add('callId')
            ->add('dt')
            ->add('did')
            ->add('clid')
            ->add('channelId')
            ->add('dtQueue')
            ->add('queue')
            ->add('dtExten')
            ->add('exten')
            ->add('dtHangup')
            ->add('callStatus')
            ->add('userfield')
            ->add('extenChannelId')
            ->add('durIvr')
            ->add('durQueue')
            ->add('durExten')
            ->add('queueCallId')
            ->add('whoCompleted')
            ->add('uid')
            ->add('uid2')
            ->add('user')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Calls::class,
        ]);
    }
}
