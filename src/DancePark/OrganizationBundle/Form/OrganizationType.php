<?php

namespace DancePark\OrganizationBundle\Form;

use DancePark\CommonBundle\EventListener\Form\DateRegularSubscriber;
use DancePark\OrganizationBundle\Form\DataTransformer\LessonToArrayTransformer;
use DancePark\OrganizationBundle\Form\DateRegularWeekWidgetType;
use DancePark\OrganizationBundle\EventListener\Form\OrganizationEventSubscriber;
use DancePark\CommonBundle\Form\DataTransformer\StringToFileTransformer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use DancePark\OrganizationBundle\Form\DataTransformer\ArrayToLessonTransformer;

class OrganizationType extends AbstractType
{
    protected $eventSubscriber;

    protected $dateRegularSubscriber;

    public function __construct (EventSubscriberInterface $eventSubscriber, DateRegularSubscriber $dateRegularSubscriber) {
        $this->eventSubscriber = $eventSubscriber;
        $dateRegularSubscriber->setFieldName('dateRegular');
        $dateRegularSubscriber->setEntityName('OrganizationBundle:DateRegularWeek');
        $this->dateRegularSubscriber = $dateRegularSubscriber;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $create = !(bool)(isset($options['data']) && (bool)$options['data']->getId());
        $builder
            ->add('name', null, array(
                'label' => 'label.name',
            ))
            ->add('logo', null, array(
                'label' => 'label.logo',
                'required' => false,
            ))
            ->add('type', null, array(
                'label' => 'label.type',
                'required' => false,
            ))
            ->add('shortDescription', 'textarea', array(
                'label' => 'label.short_description',
                'required' => false,
            ))
            ->add('description', null, array(
                'label' => 'label.description',
                'required' => false,
            ))
            ->add('dateOfIncorporation', 'ex_date', array(
                'label' => 'label.date_of_incorporation',
                'required' => false,
            ))
            ->add('webUrl', null, array(
                'label' => 'label.web_url',
                'required' => false
            ))
            ->add('phone1', null, array(
                'label' => 'label.phone1',
                'required' => false,
            ))
            ->add('phone2', null, array(
                'label' => 'label.phone2',
                'required' => false,
            ))
            ->add('email', null, array(
                'label' => 'label.email',
                'required' => false,
            ))
            ->add('dateRegular', 'collection', array(
                'label' => 'label.date_regular',
                'type' =>  new DateRegularWeekWidgetType(),
                'prototype' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'show_legend' => false,
                'widget_add_btn' => array(
                    'icon' => 'plus-sign',
                    'label' => 'add'
                ),
                'options' => array(
                    'label' => false,
                    'widget_remove_btn' => array('label' => 'remove', 'attr' => array('class' => 'btn btn-primary')),
                    'attr' => array('class' => 'span3'),
                    'widget_addon' => array(
                        'type' => 'prepend',
                        'text' => '@',
                    ),
                    'widget_control_group' => false,
                )
            ))
            ->add('lessonPrices', null, array(
                'label' => 'label.lesson_price',
                'prototype' => true,
                'allow_add' => true,
                'type' => new LessonWidgetType(),
                'widget_add_btn' => array(
                    'icon' => 'plus-sign',
                    'label' => 'add'
                ),
                'options' => array(
                    'label' => false,
                    'widget_remove_btn' => array('label' => 'remove', 'attr' => array('class' => 'btn btn-primary')),
                    'attr' => array('class' => 'span3'),
                    'widget_control_group' => true,
                )
            ))
            ->addModelTransformer(new StringToFileTransformer())
            ->addEventSubscriber($this->eventSubscriber)
        ;
        $builder->addEventSubscriber($this->dateRegularSubscriber);
        $builder->get('lessonPrices')->addModelTransformer(new ArrayToLessonTransformer());
        $builder->get('lessonPrices')->addViewTransformer(new LessonToArrayTransformer());
        if (isset($options['data']) && $options['data']->getLogo() && is_string($options['data']->getLogo())) {
             $builder->add('oldLogo', 'hidden', array(
                 'property_path' => null,
                 'mapped' => false,
                 'attr' => array(
                     'value' => $options['data']->getLogo(),
                 )
             ));
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DancePark\OrganizationBundle\Entity\Organization'
        ));
    }

    public function getName()
    {
        return 'dancepark_organizationbundle_organizationtype';
    }
}
