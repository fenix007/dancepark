<?php

namespace DancePark\DancerBundle\Form;

use DancePark\DancerBundle\Form\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DancerEventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add($builder->create('date', null, array(
                'label' => 'label.date'
            ))->addModelTransformer(new DateTimeToStringTransformer("Y-m-d H:i:s")))
            ->add('status', null, array(
                'label' => 'label.status'
            ))
            ->add('dancer', null, array(
                'label' => 'label.dancer',
                'required' => false,
            ))
            ->add('event', null, array(
                'label' => 'label.event',
                'required' => false,
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DancePark\DancerBundle\Entity\DancerEvent'
        ));
    }

    public function getName()
    {
        return 'dancepark_dancerbundle_dancereventtype';
    }
}
