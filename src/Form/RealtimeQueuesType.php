<?php

namespace App\Form;

use App\Entity\RealtimeQueues;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RealtimeQueuesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('musiconhold')
            ->add('announce')
            ->add('context')
            ->add('timeout')
            ->add('ringinuse')
            ->add('setinterfacevar')
            ->add('setqueuevar')
            ->add('setqueueentryvar')
            ->add('monitorFormat')
            ->add('membermacro')
            ->add('membergosub')
            ->add('queueYouarenext')
            ->add('queueThereare')
            ->add('queueCallswaiting')
            ->add('queueQuantity1')
            ->add('queueQuantity2')
            ->add('queueHoldtime')
            ->add('queueMinutes')
            ->add('queueMinute')
            ->add('queueSeconds')
            ->add('queueThankyou')
            ->add('queueCallerannounce')
            ->add('queueReporthold')
            ->add('announceFrequency')
            ->add('announceToFirstUser')
            ->add('minAnnounceFrequency')
            ->add('announceRoundSeconds')
            ->add('announceHoldtime')
            ->add('announcePosition')
            ->add('announcePositionLimit')
            ->add('periodicAnnounce')
            ->add('periodicAnnounceFrequency')
            ->add('relativePeriodicAnnounce')
            ->add('randomPeriodicAnnounce')
            ->add('retry')
            ->add('wrapuptime')
            ->add('penaltymemberslimit')
            ->add('autofill')
            ->add('monitorType')
            ->add('autopause')
            ->add('autopausedelay')
            ->add('autopausebusy')
            ->add('autopauseunavail')
            ->add('maxlen')
            ->add('servicelevel')
            ->add('strategy')
            ->add('joinempty')
            ->add('leavewhenempty')
            ->add('reportholdtime')
            ->add('memberdelay')
            ->add('weight')
            ->add('timeoutrestart')
            ->add('defaultrule')
            ->add('timeoutpriority')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RealtimeQueues::class,
        ]);
    }
}
