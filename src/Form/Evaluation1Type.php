<?php

namespace App\Form;

use App\Entity\Evaluation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Evaluation1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sourceDestID')
            ->add('score')
            ->add('comment')
            ->add('evaluatorComment')
            ->add('evaluativeComment')
            ->add('duration')
            ->add('phoneNumber')
            ->add('callDate')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('deletedAt')
            ->add('formTemplate')
            ->add('user')
            ->add('evaluative')
            ->add('source')
            ->add('resetReason')
            ->add('status')
            ->add('evaluationReasonType')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Evaluation::class,
        ]);
    }
}
