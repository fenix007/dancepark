<?php

namespace DancePark\UserBundle\Form;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    protected $subscriber;

    /**
     * @param EventSubscriberInterface $subscriber
     */
    public function __construct(EventSubscriberInterface $subscriber)
    {
        $this->subscriber = $subscriber;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $create = !(isset($options['data']) && $options['data']->getId());
        $builder
            ->add('email', null, array(
                'label' => 'label.email'
            ))
            ->add('enabled', null, array(
                'label' => 'label.enabled',
                'required' => false,
            ))
            ->add('plainPassword', null, array(
                'label' => 'label.password',
                'required' => $create,
            ))
            ->add('locked', null, array(
                'label' => 'label.locked',
                'required' => false
            ))
            ->add('expired', null, array(
                'label' => 'label.expired',
                'required' => false,
            ))
            ->add('roles', 'choice', array(
                'required' => false,
                'label' => 'label.roles',
                'multiple' => true,
                'choices' => array(
                    'ROLE_USER' => 'User',
                    'ROLE_MODERATOR' => 'Moderator',
                )
            ))
        ;
        $builder->addEventSubscriber($this->subscriber);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DancePark\UserBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'dancepark_userbundle_usertype';
    }
}
