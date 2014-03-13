<?php
namespace DancePark\FrontBundle\Form\Type;

use DancePark\DancerBundle\Entity\Dancer;
use DancePark\DancerBundle\Form\DataTransformer\DancerToStringTransformer;
use DancePark\FrontBundle\EventListener\Form\DigestEventSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DigestType extends AbstractType
{
    /** @var $dancer Dancer */
    protected $dancer;

    public function __construct(Dancer $dancer = null)
    {
        $this->dancer = $dancer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('organization', null, array(
                'label' => 'label.organization',
                'required' => false
            ))
        // Additional fields
            ->add('event', null, array(
                'label' => 'label.event',
                'required' => false,
                'attr' => array(
                    'class' => 'field-optional'
                ),
            ))
            ->add('place', null, array(
                'label' => 'label.place',
                'required' => false,
                'attr' => array(
                    'class' => 'field-optional'
                ),
            ))
            ->add('danceType', null, array(
                'label' => 'label.dance_type',
                'required' => false,
                'attr' => array(
                    'class' => 'field-optional'
                ),
            ))
            ->add('eventType', null, array(
                'label' => 'label.event_type',
                'required' => false,
                'attr' => array(
                    'class' => 'field-optional'
                ),
            ))
            ->add('dateTo', 'ex_date', array(
                'label' => 'label.date_to',
                'required' => false,
                'widget' => 'text',
                'attr' => array(
                    'class' => 'field-optional'
                ),
            ))
            ->add('dateFrom', 'ex_date', array(
                'label' => 'label.date_from',
                'required' => false,
                'widget' => 'text',
                'attr' => array(
                    'class' => 'field-optional'
                ),
            ))
            ->add('startTime', 'time', array(
                'label' => 'label.start_time',
                'required' => false,
                'widget' => 'text',
                'attr' => array(
                    'class' => 'field-optional'
                ),
            ))
            ->add('endTime', 'time', array(
                'label' => 'label.end_time',
                'required' => false,
                'widget' => 'text',
                'attr' => array(
                    'class' => 'field-optional'
                ),
            ))
            ->add('metro', null, array(
                'label' => 'label.metro',
                'required' => false,
                'attr' => array(
                    'class' => 'field-optional'
                ),
            ))
            ->add('address', null, array(
                'label' => 'label.address',
                'required' => false,
                'attr' => array(
                    'class' => 'field-optional'
                ),
            ))
        ;
        if (!$this->dancer) {
            $builder->add('email', 'email', array(
                    'label' => 'label.email',
                ))
            ;
        } else {
            $builder->add('dancer', 'hidden', array(
                    'empty_data' => $this->dancer->getId(),
                ))
            ;
        }
        $builder->addEventSubscriber(new DigestEventSubscriber($this->dancer));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DancePark\DancerBundle\Entity\Digest'
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'create_digest_type';
    }
}