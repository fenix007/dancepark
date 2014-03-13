<?php

namespace DancePark\EventBundle\Form;

use DancePark\EventBundle\Entity\DateRegularWeek;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DateRegularWeekWidgetType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dayOfWeek', 'choice', array(
                'label' => 'label.day_of_week',
                'choices' => DateRegularWeek::getDaysOfWeek(),
            ))
            ->add('startTime', 'time', array(
                'label' => 'label.start_time',
            ))
            ->add('endTime', 'time', array(
                'label' => 'label.end_time',
            ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DancePark\EventBundle\Entity\DateRegularWeek'
        ));
    }

    public function getName()
    {
        return 'dancepark_eventbundle_dateregularweektype';
    }
}
