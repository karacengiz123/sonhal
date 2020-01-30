<?php

namespace App\Form;

use App\Entity\FormCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('minScore')
            ->add('sort')
            ->add('maxScore')
            ->add('formTemplate')
            ->add('formSection')
            ->add('formQuestions')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FormCategory::class,
        ]);
    }
}
