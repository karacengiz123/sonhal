<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserNoneLdapType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username',null,[
                "label"=> "Kullanıcı Adı"])

            ->add('email',null,[
                "label"=> "Email"])

            ->add('password',PasswordType::class,[
                "label"=> "Şifre"])

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
