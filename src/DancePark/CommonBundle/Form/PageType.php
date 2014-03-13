<?php

namespace DancePark\CommonBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
          ->add('title', null, array(
              'label' => 'label.title'
          ))
          ->add('text', 'textarea', array(
              'required' => false,
              'label' => 'label.text'
          ))
          ->add('active', null, array(
              'label' => 'label.active',
          ))
          ->add('path', null, array(
              'label' => 'label.path'
          ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DancePark\CommonBundle\Entity\Page'
        ));
    }

    public function getName()
    {
        return 'dancepark_commonbundle_pagetype';
    }
}
