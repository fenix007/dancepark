<?php

namespace DancePark\DancerBundle\Form;

use DancePark\DancerBundle\Form\DataTransformer\DancerToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DigestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('organization', null, array(
                'label' => 'label.organization',
                'required' => false,
            ))
            ->add('event', null, array(
                'label' => 'label.event',
                'required' => false,
            ))
            ->add('place', null, array(
                'label' => 'label.place',
                'required' => false,
            ))
            ->add('danceType', null, array(
                'label' => 'label.dance_type',
                'required' => false,
            ))
            ->add('eventType', null, array(
                'label' => 'label.event_type',
                'required' => false,
            ))
            ->add('dateTo', 'ex_date', array(
                'label' => 'label.date_to',
                'required' => false,
            ))
            ->add('dateFrom', 'ex_date', array(
                'label' => 'label.date_from',
                'required' => false,
            ))
            ->add('startTime', 'time', array(
                'label' => 'label.start_time',
                'required' => false,
            ))
            ->add('endTime', 'time', array(
                'label' => 'label.end_time',
                'required' => false,
            ))
            ->add('metro', null, array(
                'label' => 'label.metro',
                'required' => false,
            ))
            ->add('address', null, array(
                'label' => 'label.address',
                'required' => false,
            ))
            ->add('email', 'email', array(
                'label' => 'label.email',
                'required' => false,
            ))
            ->add('dancer', null, array(
                'label' => 'label.dancer',
                'required' => false,
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DancePark\DancerBundle\Entity\Digest'
        ));
    }

    public function getName()
    {
        return 'dancepark_dancerbundle_digesttype';
    }
}
