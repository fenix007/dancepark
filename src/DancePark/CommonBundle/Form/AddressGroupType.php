<?php

namespace DancePark\CommonBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AddressGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                'label' => 'label.name',
            ))
            ->add('prefix', null, array(
                'label' => 'label.prefix',
                'required' => false,
            ))
            ->add('parent', null, array(
                'label' => 'label.parent',
                'required' => false,
            ))
            ->add('addressLevel', null, array(
                'label' => 'label.address_label',
            ))
            ->add('region', null, array(
                'label' => 'label.address_region'
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DancePark\CommonBundle\Entity\AddressGroup'
        ));
    }

    public function getName()
    {
        return 'dancepark_commonbundle_addressgrouptype';
    }
}
