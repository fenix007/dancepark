<?php

namespace DancePark\CommonBundle\Form;

use DancePark\CommonBundle\Entity\SearchKeywords;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SearchKeywordsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                'label' => 'label.name'
            ))
            ->add('type', 'choice', array(
                'label' => 'label.type',
                'choices' => SearchKeywords::getKeywordsTypes()
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DancePark\CommonBundle\Entity\SearchKeywords'
        ));
    }

    public function getName()
    {
        return 'dancepark_commonbundle_searchkeywordstype';
    }
}
