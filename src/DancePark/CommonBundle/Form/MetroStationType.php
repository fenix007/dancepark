<?php

namespace DancePark\CommonBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MetroStationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                'label' => 'label.name',
            ))
            ->add('latitude', null, array(
                'label' => 'label.latitude'
            ))
            ->add('longtitude', null, array(
                'label' => 'label.longtitude'
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DancePark\CommonBundle\Entity\MetroStation'
        ));
    }

    public function getName()
    {
        return 'dancepark_commonbundle_metrostationtype';
    }
}
