<?php

namespace DancePark\DancerBundle\Form;

use DancePark\DancerBundle\Form\DataTransformer\DancerToStringTransformer;
use DancePark\DancerBundle\Form\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FeedbackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('summary', 'textarea', array(
                'label' => 'label.summary'
            ))
            ->add('dancer', null, array(
                'label' => 'label.dancer',
                'required' => false,
            ))
            ->add('positive', null, array(
                'label' => 'label.positive',
                'required' => false,
            ))
            ->add('negative', null, array(
                'label' => 'label.negative',
                'required' => false,
            ))
            ->add('rating', null, array(
                'label' => 'label.rating'
            ))
            ->add('organization', null, array(
                'label' => 'label.organization'
            ))
            ->add('place', null, array(
                'label' => 'label.place'
            ))
            ->add('event', null, array(
                'label' => 'label.event'
            ))
            ->add('dancer', 'genemu_jqueryautocomplete_text', array(
                'label' => 'label.dancer',
                'route_name' => 'dancer_email_api',
                'required' => false,
            ))
        ;
        $builder->get('dancer')->addModelTransformer(new DancerToStringTransformer());
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DancePark\DancerBundle\Entity\Feedback'
        ));
    }

    public function getName()
    {
        return 'dancepark_dancerbundle_feedbacktype';
    }
}
