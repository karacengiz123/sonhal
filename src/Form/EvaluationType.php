<?php

namespace App\Form;

use App\Entity\Evaluation;
use App\Entity\Team;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EvaluationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('formTemplate', null,[
                "label"=> "Süreç",
                "attr"=> ["width"=>"100%"]
            ])
            ->add('user', null ,[
                "label"=>"Temsilci",
            ])
            ->add('source',null,[
                "label"=>"Değerlendirme Tipi",
            ])
            ->add('sourceDestID',null,[
                "label"=>"Aktivite ID",
                "attr"=> ["class"=>"form-control",
                'required'=>'required']
            ])
            ->add('citizenID',null,[
                "label"=>"Vatandaş ID",
                "attr"=> ["class"=>"form-control",
                   ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {

        $resolver->setDefaults([
            'data_class' => Evaluation::class,
        ]);
    }
}
