<?php

namespace DancePark\EventBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EventLessonPriceWidgetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lesson', null, array(
                'label' => 'label.lesson'
            ))
            ->add('price', null, array(
                'label' => 'label.price'
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DancePark\EventBundle\Entity\EventLessonPrice'
        ));
    }

    public function getName()
    {
        return 'dancepark_eventbundle_eventlessonpricetype';
    }
}
