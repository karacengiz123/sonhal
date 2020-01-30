<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username',null,[
                "label"=> "Kullanıcı Adı"])

            ->add('email',null,[
                "label"=> "Email"])

            ->add('groups',null,[
                "label"=> "Gruplar"])

            ->add('breakGroup',null,[
                "label"=> "Mola Grubu"])

            ->add('teamId',null,[
                "label"=> "Takım ID"])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
