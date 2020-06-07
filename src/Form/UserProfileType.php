<?php

namespace App\Form;

use App\Entity\UserProfile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tckn',null,[
                "label"=> "Tc Kimlik No"])

            ->add('firstName',null,[
                "label"=> "İsim"])

            ->add('lastName',null,[
                "label"=> "Soy İsim"])

            ->add('extension',null,[
                "label"=> "Dahili No"])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserProfile::class,
        ]);
    }
}