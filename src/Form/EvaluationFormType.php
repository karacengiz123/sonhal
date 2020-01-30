<?php

namespace App\Form;

use App\Entity\Evaluation;
use App\Entity\EvaluationReasonType;
use App\Entity\EvaluationResetReason;
use App\Entity\EvaluationStatus;
use App\Entity\Team;
use App\Repository\EvaluationResetReasonRepository;
use Doctrine\ORM\EntityRepository;
use JsonSchema\Validator;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Clue\StreamFilter\fun;
use function Doctrine\ORM\QueryBuilder;


class EvaluationFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $getFormTemlateId = $options["data"]->getFormTemplate()->getId();

        $builder
            ->add('comment',null,["attr" => ['style' => 'resize:vertical; min-height: 220px;']])
            ->add('duration', NumberType::class)
//            ->add('evaluationExtraSources')
            ->add('resetReason'
                ,null,[
                'class'=>EvaluationResetReason::class,
                'query_builder'=>
                /**
                 * @var $getFormTemlate
                 */
                function(EvaluationResetReasonRepository $er) use ( $getFormTemlateId )
                {
                    $reason = $er->createQueryBuilder('r');
                    return $reason
                        ->where( $reason->expr()->like("r.forms",":forms"))
                        ->setParameter("forms","%".$getFormTemlateId."%");
                }
                ,'attr'=>['style'=>'width:250px']
                    ])
            ->add('evaluatorComment')
            ->add('createdAt')
            ->add('evaluationType',null,['required'=> true ])
            ->add('evaluativeComment',null,["attr" => ['style' => 'resize:vertical']])
            ->add('rejectComment',null,["attr" => ['style' => 'resize:vertical']])
            ->add('rejectCloseComment',null,["attr" => ['style' => 'resize:vertical']])
            ->add('status', null , ["attr" => ["class"=>"evaluationStatus", 'style' => 'display:none']])
            ->add('evaluationReasonType',null,['required'=> true ])
            ->add('callDate', null,["attr" => ["class"=>"input-group date"]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Evaluation::class,
        ]);
    }
}
