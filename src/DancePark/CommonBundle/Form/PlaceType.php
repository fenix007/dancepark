<?php

namespace DancePark\CommonBundle\Form;

use DancePark\CommonBundle\EventListener\Form\PlaceEventSubscriber;
use DancePark\CommonBundle\Form\DataTransformer\AddressTransformer;
use DancePark\CommonBundle\Form\DataTransformer\GetPathNormalizerDataTransformer;
use DancePark\CommonBundle\Form\DataTransformer\StringToFileTransformer;
use DancePark\CommonBundle\Form\HowToGetWidgetType;
use DancePark\CommonBundle\Form\DataTransformer\ArrayToGetPathTransformer;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PlaceType extends AbstractType
{
    /** @var $em EntityManager */
    protected $em;

    protected $checkMetroStationEventSubscriber;

    public function __construct($checkMetroStationEventSubscriber, EntityManager $em)
    {
        $this->em = $em;
        $this->checkMetroStationEventSubscriber = $checkMetroStationEventSubscriber;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $create = !(bool)(isset($options['data']) && (bool)$options['data']->getId());

        $cityQueryBuilder = $this->em->createQueryBuilder();
        $cityQueryBuilder
            ->select('ga')
            ->from('CommonBundle:AddressGroup', 'ga')
            ->where('ga.parent IS NULL');

        $groupQueryBuilder = $this->em->createQueryBuilder();
        $groupQueryBuilder
            ->select('ga')
            ->from('CommonBundle:AddressGroup', 'ga')
            ->where('ga.parent IS NOT NULL');

        $builder
            ->add('name', null, array(
                'label' => 'label.name',
            ))
            ->add('logo', null, array(
                'label' => 'label.logo',
                'required' => false,
            ))
            ->add('address', 'genemu_jquerygeolocation', array(
                'label' => 'label.address'
            ))
            ->add('city_id', null, array(
                'label' => 'label.city_id',
                'required' => false,
                'query_builder' => $cityQueryBuilder,
            ))
            ->add('addrGroup', 'genemu_jqueryautocomplete_text', array(
                'label' => 'label.add_group',
                'required' => false,
                'data_class' => 'DancePark\CommonBundle\Entity\AddressGroup',
                'route_name' => 'address_street_autocomplete'
            ))
            ->add('danceType', null, array(
                'label' => 'label.dance_type',
                'multiple' => true,
                'required' => false,
            ))
            ->add('metro', null, array(
                'label' => 'label.metro',
                'required' => false
            ))
            ->add('latitude', 'text', array(
                'label' => 'label.latitude',
                'required' => false
            ))
            ->add('longtitude', 'text', array(
                'label' => 'label.longtitude',
                'required' => false
            ))
            ->add('descriptionTogo', null, array(
                'label' => 'label.description_togo'
            ))
            ->add('webUrl', null, array(
                'label' => 'label.url',
                'required' => false
            ))
            ->add('phone1', null, array(
                'label' => 'label.phone1',
                'required' => false
            ))
            ->add('phone2', null, array(
                'label' => 'label.phone2',
                'required' => false
            ))
            ->add('email', null, array(
                'label' => 'label.email'
            ))
            ->add('addOptions', null, array(
                'label' => 'label.add_options',
                'required' => false
            ))
            ->add('addEvent', null, array(
                'label' => 'label.add_event',
                'required' => false
            ))
            ->add('howToGet', null, array(
                'label' => 'label.howToGet',
                'prototype' => true,
                'allow_add' => true,
                'type' => new HowToGetWidgetType(),
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
            ->add('organizations', null, array(
                'label' => 'label.organization',
                'required' => false,
                'multiple' => true,
            ))
        ;
        $builder->get('howToGet')->addModelTransformer(new ArrayToGetPathTransformer());
        $builder->get('howToGet')->addViewTransformer(new GetPathNormalizerDataTransformer());
        $builder->addModelTransformer(new StringToFileTransformer());
        $builder->addModelTransformer(new AddressTransformer($this->em));
        $builder->addEventSubscriber($this->checkMetroStationEventSubscriber);
        $builder->addEventSubscriber(new PlaceEventSubscriber());
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
            'data_class' => 'DancePark\CommonBundle\Entity\Place'
        ));
    }

    public function getName()
    {
        return 'dancepark_commonbundle_placetype';
    }
}
