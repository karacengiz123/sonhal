<?php

namespace App\Form;

use App\Entity\GuideGroup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GuideGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class, [
                "label" => "Telefon No",
                "attr" => ["class"=>"form-control"]
            ])
            ->add('route',TextType::class, [
            "label" => "Telefon No",
                "attr" => ["class"=>"form-control"]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => GuideGroup::class,
        ]);
    }
}
