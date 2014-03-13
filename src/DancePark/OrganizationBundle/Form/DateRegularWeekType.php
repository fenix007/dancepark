<?php

namespace DancePark\OrganizationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DateRegularWeekType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dayOfWeek')
            ->add('startTime')
            ->add('endTime')
            ->add('organization')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DancePark\OrganizationBundle\Entity\DateRegularWeek'
        ));
    }

    public function getName()
    {
        return 'dancepark_organizationbundle_dateregularweektype';
    }
}
