<?php

namespace DancePark\DancerBundle\Form;

use DancePark\DancerBundle\Entity\Dancer;
use DancePark\DancerBundle\EventListener\Form\DancerEventSubscriber;
use DancePark\DancerBundle\EventListener\Form\DancerDanceTypeEventSubscriber;
use DancePark\DancerBundle\Form\DataTransformer\DateTimeToStringTransformer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Propel\UserManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use DancePark\DancerBundle\Form\DataTransformer\StringToFileTransformer;

class DancerType extends AbstractType
{
    protected $subscriber;

    protected $em;

    protected $danceTypeEventSubscriber;
    /**
     * @param EventSubscriberInterface $subscriber
     */
    public function __construct(
        EventSubscriberInterface $subscriber,
        EntityManager $em,
        DancerDanceTypeEventSubscriber $danceTypeSubscriber)
    {
        $this->subscriber = $subscriber;
        $this->em = $em;
        $this->danceTypeEventSubscriber = $danceTypeSubscriber;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $dancerRoles = Dancer::getDancerRoles();
        $create = !(isset($options['data']) && $options['data']->getId());
        $danceTypes = $this->em->createQueryBuilder()->select('dt')->from('CommonBundle:DanceType', 'dt');
        $data = $options['data'];
        $defaultDanceTypesRaw = $data->getDanceType();
        $defaultDanceTypes = new ArrayCollection();
        if (!empty($defaultDanceTypesRaw)) {
            foreach($defaultDanceTypesRaw as $dt) {
                $defaultDanceTypes[] = $dt->getdanceType();
            }
            $data->setDanceType($defaultDanceTypes);
        }
        $builder
              ->add('firstName', null, array(
                  'label' => 'label.first_name'
              ))
              ->add('lastName', null, array(
                 'label' => 'label.last_name'
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
              ->add('photo', null, array(
                  'label' => 'label.photo',
                  'required' => false,
              ))
            ->add('roles', 'choice', array(
                'label' => 'label.roles',
                'choices' => $dancerRoles,
                'expanded' => true,
                'multiple' => true,
            ))
            ->add($builder->create('danceType', 'entity', array(
                    'label' => 'label.dance_type',
                    'class' => 'DancePark\CommonBundle\Entity\DanceType',
                    'required' => false,
                    'multiple' => true,
                    'query_builder' => $danceTypes,
                ))
            )
            ->add('email', null, array(
                'label' => 'label.email'
            ))
            ->add('weight', null, array(
                 'label' => 'label.weight'
            ))
            ->add('height', null, array(
                'label' => 'label.height'
            ))
            ->add('is_pro', 'choice', array(
                'choices' => array(
                    1 => 'Pro',
                    0 => 'Am',
                ),
                'expanded' => true,
                'label' => 'label.is_pro'
            ))
            ->add('findPartner', null, array(
                'label' => 'label.find_partner'
            ))
            ->add('organizations', null, array(
                'label' => 'label.organization',
                'required' => false,
                'multiple' => true,
            ))
            ->add($builder->create('birthday', null, array(
                'label' => 'label.birthday',
                 'required' => false,))
                ->addModelTransformer(new DateTimeToStringTransformer('Y-m-d'))
            )
            ->add($builder->create('startToDance', null, array(
                  'label' => 'label.start_to_dance',
                  'required' => false,))
                ->addModelTransformer(new DateTimeToStringTransformer('Y-m-d'))
            )
            ->add('phone1', null, array(
                'label' => 'label.phone1',
                'required' => false,
            ))
            ->add('phone2', null, array(
                'label' => 'label.phone2',
                'required' => false
            ))
            ->add('biography', null, array(
                'label' => 'label.biography',
                'required' => false
            ))
            ->add('shortOverview', null, array(
                'label' => 'label.short_overview',
                'required' => false
            ))
            ->add('danceClub', null, array(
                'label' => 'label.dance_club',
                'required' => false
            ))
        ;
        if (isset($options['data']) && $options['data']->getPhoto() && is_string($options['data']->getPhoto())) {
            $builder->add('oldPhoto', 'hidden', array(
                'property_path' => null,
                'mapped' => false,
                'attr' => array(
                    'value' => $options['data']->getPhoto(),
                )
            ));
        }
        $builder->addEventSubscriber($this->danceTypeEventSubscriber);
        $builder->addModelTransformer(new StringToFileTransformer());
        $builder->addEventSubscriber($this->subscriber);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DancePark\DancerBundle\Entity\Dancer'
        ));
    }

    public function getName()
    {
        return 'dancepark_dancerbundle_dancertype';
    }
}
