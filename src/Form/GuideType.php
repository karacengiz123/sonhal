<?php

namespace App\Form;

use App\Entity\Guide;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GuideType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('phone',TextType::class, [
                "label" => "Telefon No",
                "attr" => ["class"=>"form-control"]
            ])
            ->add('nameSurname',TextType::class, [
                "label" => "Adı Soyadı",
                "attr" => ["class"=>"form-control"]
            ])
            ->add('guideGroupID',null, [
                "label" => "Grup id",
                "attr" => ["class"=>"form-control"]
            ])
            ->add('title',TextType::class, [
                "label" => "Ünvan",
                "attr" => ["class"=>"form-control"]
            ])
            ->add('targetType',ChoiceType::class, [
                "label" => "Hedef Tipi",
                "attr" => ["class"=>"form-control"],
                'choices' => [
                    'Friend' => 'friend',
                    'Guide' => 'guide'
                ],
            ])
            ->add('targetId',ChoiceType::class, [
                "label" => "Hedef Numarası",
                "attr" => ["class"=>"form-control"]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Guide::class,
        ]);
    }
}
