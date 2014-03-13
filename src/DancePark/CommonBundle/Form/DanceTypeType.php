<?php

namespace DancePark\CommonBundle\Form;

use DancePark\CommonBundle\Entity\DanceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DanceTypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                'label' => 'label.name'
            ))
            ->add('kind', 'choice', array(
                'label' => 'label.kind',
                'choices' => DanceType::getAvaliableKinds(),
            ))
            ->add('danceGroup', null, array(
                'label' => 'label.dance_group',
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DancePark\CommonBundle\Entity\DanceType'
        ));
    }

    public function getName()
    {
        return 'dancepark_commonbundle_dancetypetype';
    }
}
