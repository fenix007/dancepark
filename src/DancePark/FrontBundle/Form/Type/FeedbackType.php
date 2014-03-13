<?php
namespace DancePark\FrontBundle\Form\Type;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FeedbackType extends AbstractType
{
    protected $eventSubscriber;

    public function __construct(EventSubscriberInterface $eventSubsciber)
    {
        $this->eventSubscriber = $eventSubsciber;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('summary', 'textarea', array(
                'label' => 'label.summary',
                'required' => false,
            ))
            ->add('positive', 'textarea', array(
              'label' => 'label.positive',
              'required' => false,
            ))
            ->add('negative', 'textarea', array(
              'label' => 'label.negative',
              'required' => false,
            ))
            ->add('rating', 'choice', array(
                'label' => 'label.rating',
                'choices' => array(
                    1 => 'label.terribly',
                    2 => 'label.bad',
                    3 => 'label.normal',
                    4 => 'label.good',
                    5 => 'label.wonderful',
                ),
                'empty_data' => 5,
                'expanded' => true,
            ))
        ;
        $builder->addEventSubscriber($this->eventSubscriber);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DancePark\DancerBundle\Entity\Feedback'
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'front_feedback_type';
    }
}