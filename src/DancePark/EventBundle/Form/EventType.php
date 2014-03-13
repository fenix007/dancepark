<?php

namespace DancePark\EventBundle\Form;

use DancePark\CommonBundle\EventListener\Form\DateRegularSubscriber;
use DancePark\CommonBundle\Form\DataTransformer\StringToFileTransformer;
use DancePark\EventBundle\Entity\DateRegularWeek;
use DancePark\EventBundle\Entity\Event;
use DancePark\EventBundle\EventListener\Form\EventEventSubscriber;
use DancePark\EventBundle\Form\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EventType extends AbstractType
{
    protected $eventSubscriber;

    protected $dateRegularSubscriber;

    public function __construct(EventSubscriberInterface $subscriber, DateRegularSubscriber $dateRegularSubscriber)
    {
        $this->eventSubscriber = $subscriber;
        $dateRegularSubscriber->setEntityName('EventBundle:DateRegularWeek');
        $dateRegularSubscriber->setFieldName('dateRegular');
        $this->dateRegularSubscriber = $dateRegularSubscriber;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                'label' => 'label.name'
            ))
            ->add('kind', 'choice', array(
                'label' => 'label.kind',
                'choices' => Event::getEventKinds()
            ))
            ->add('logo', null, array(
                'label' => 'label.logo',
                'required' => false,
            ))
            ->add('short_info', null, array(
                'label' => 'label.short_info',
                'required' => false,
            ))
            ->add(
                $builder->create('date', 'date', array(
                    'label' => 'label.date',
                    'required' => false
                    ))
            )
            ->add($builder->create('startTime', null, array(
                'label' => 'label.start_time',)
                )->addModelTransformer(new DateTimeToStringTransformer("H:i:s"))
            )
            ->add($builder->create('endTime', null, array(
                'label' => 'label.end_time',
                ))->addModelTransformer(new DateTimeToStringTransformer("H:i:s"))
            )
            ->add('price', 'number', array(
                'label' => 'label.price',
                'required' => false
            ))
            ->add('children', null, array(
                'label' => 'label.children',
                'required' => false,
            ))
            ->add('abonement', null, array(
                'label' => 'label.abonement',
                'required' => false,
            ))
            ->add('recommended', null, array(
                'label' => 'label.recommended',
                'required' => false,
            ))
            ->add('infoColumn', null, array(
                'label' => 'label.info_column',
            ))
            ->add('description', null, array(
                'label' => 'label.description'
            ))
            ->add('type', null, array(
                'label' => 'label.type',
            ))
            ->add('danceType', null, array(
                'label' => 'label.dance_type',
                'multiple' => true,
                'required' => false,
            ))
            ->add('organizations', null, array(
                'label' => 'label.organization',
                'required' => false,
                'multiple' => true,
            ))
            ->add('place', null, array(
                'label' => 'label.place'
            ))
            ->add('training', null, array(
                'label' => 'label.training',
                'required' => false,
            ))
            ->add('teachers', null, array(
                'label' => 'label.teachers',
                'required' => false,
            ))
            ->add('dateRegular', 'collection', array(
                'label' => 'label.date_regular',
                'type' =>  new DateRegularWeekWidgetType(),
                'prototype' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
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
            ->add('lessonPrices', 'collection', array(
                'label' => 'label.lesson_price',
                'type' =>  new EventLessonPriceWidgetType(),
                'prototype' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
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
        ;
        $builder->addEventSubscriber($this->dateRegularSubscriber);
        $builder->addModelTransformer(new StringToFileTransformer());
        $builder->addEventSubscriber($this->eventSubscriber);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DancePark\EventBundle\Entity\Event'
        ));
    }

    public function getName()
    {
        return 'dancepark_eventbundle_eventtype';
    }
}
