<?php

namespace DancePark\CommonBundle\Form;

use DancePark\CommonBundle\EventListener\Event\EntityEvent;
use DancePark\CommonBundle\EventListener\Form\DancerAutocompleteSubscriber;
use DancePark\DancerBundle\Form\DataTransformer\DancerToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ArticleType extends AbstractType
{
    protected $dancerAutocomplete;

    public function __construct(DancerAutocompleteSubscriber $subscriber)
    {
        $this->dancerAutocomplete = $subscriber;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, array(
                'label' => 'label.title'
            ))
            ->add('body', null, array(
                'label' => 'label.body'
            ))
            ->add('organization', null, array(
                'label' => 'label.organization'
            ))
            ->add('author', 'genemu_jqueryautocomplete_text', array(
                'label' => 'label.dancer',
                'route_name' => 'dancer_email_api',
                'required' => false,
            ))
            ->add('event', null, array(
                'label' => 'label.event'
            ))
            ->add('place', null, array(
                'label' => 'label.place'
            ))
            ->add('danceType', null, array(
                'label' => 'label.dance_type'
            ))
        ;
        $builder->get('author')->addModelTransformer(new DancerToStringTransformer());
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DancePark\CommonBundle\Entity\Article'
        ));
    }

    public function getName()
    {
        return 'dancepark_commonbundle_articletype';
    }
}
