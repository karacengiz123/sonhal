<?php

namespace App\Form;

use App\Entity\ChatMessage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChatMessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sender')
            ->add('message')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('deletedAt')
            ->add('chat')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ChatMessage::class,
        ]);
    }
}
