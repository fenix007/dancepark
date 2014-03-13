<?php

namespace DancePark\EventBundle\Form;

use DancePark\EventBundle\Form\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EventClosingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add($builder->create('begin', null, array(
                'label' => 'label.begin',

            ))->addModelTransformer(new DateTimeToStringTransformer('Y-m-d H:i:s')))
            ->add($builder->create('end', null, array(
                'label' => 'label.end'
            ))->addModelTransformer(new DateTimeToStringTransformer('Y-m-d H:i:s')))
            ->add('reason', null, array(
                'label' => 'label.reason',
            ))
            ->add('event', null, array(
                'label' => 'label.event'
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DancePark\EventBundle\Entity\EventClosing'
        ));
    }

    public function getName()
    {
        return 'dancepark_eventbundle_eventclosingtype';
    }
}
