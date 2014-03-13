<?php
namespace DancePark\FrontBundle\Form\Type;

use DancePark\CommonBundle\Entity\DanceType;
use DancePark\DancerBundle\EventListener\Form\DancerDanceTypeEventSubscriber;
use DancePark\DancerBundle\Form\DataTransformer\StringToFileTransformer;
use DancePark\FrontBundle\EventListener\Form\DancerProfileSubscriber;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DancerProfileType extends AbstractType
{
    protected $dancerSubscriber;
    protected $profileSubscriber;
    protected $em;


    /**
     * @param EventSubscriberInterface $subscriber
     */
    public function __construct(
        EventSubscriberInterface $dancerSubscriber,
        EventSubscriberInterface $profileSubscriber,
        EntityManager $em
    )
    {
        $this->dancerSubscriber = $dancerSubscriber;
        $this->profileSubscriber = $profileSubscriber;
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $danceTypes = $this->em->createQueryBuilder()->select('dt')->from('CommonBundle:DanceType', 'dt');
        $data = $options['data'];
        $defaultDanceTypesRaw = $data->getDanceType();
        $defaultDanceTypes = new ArrayCollection();
        $proInfo = array();
        if (!empty($defaultDanceTypesRaw)) {
            foreach($defaultDanceTypesRaw as $dt) {
                $defaultDanceTypes[$dt->getDanceType()->getId()] = $dt->getDanceType();
                $proInfo[$dt->getDanceType()->getId()] = $dt->getIsPro();
            }
            $data->setDanceType($defaultDanceTypes);
        }
        $builder
            ->add('is_pro', 'checkbox', array(
                'label' => 'label.is_pro',
                'required' => false
            ))
            ->add('firstName', null, array(
                'label' => 'label.first_name',
            ))
            ->add('lastName', null, array(
                'label' => 'label.last_name',
            ))
            ->add('photo', null, array(
                'label' => 'label.photo',
                'required' => false,
            ))
            ->add('findPartner', null, array(
                'label' => 'label.find_partner',
                'required' => false,
            ))
            ->add('birthday', 'ex_date', array(
                'label' => 'label.birthday'
            ))
            ->add('startToDance', 'ex_date', array(
                'label' => 'label.start_to_dance'
            ))
            ->add('weight', null, array(
                'label' => 'label.weight_form'
            ))
            ->add('height', null, array(
                'label' => 'label.height_form'
            ))
            ->add('phone1', null, array(
                'label' => 'label.phone1'
            ))
            ->add('phone2', null, array(
                'label' => 'label.phone2'
            ))
            ->add('danceClub', null, array(
                'label' => 'label.dance_club',
                'required' => false,
            ))
            ->add('shortOverview', null, array(
                'label' => 'label.short_overview'
            ))
            ->add('biography', 'textarea',array(
                'label' => 'label.biography'
            ))
            ->add('danceType', 'entity', array(
                'label' => 'label.danceType',
                'class' => 'DancePark\CommonBundle\Entity\DanceType',
                'required' => false,
                'query_builder' => $danceTypes,
                'multiple' => true,
                'empty_data' => $defaultDanceTypes
            ))
            ->add('danceTypesHidden', 'hidden', array(
                'mapped' => false,
                'required' => false,
                'data' => '[]'
            ))
            ->add('pro_info', 'hidden', array(
                'mapped' => false,
                'required' => false,
                'data' => json_encode($proInfo),
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
        $builder->addModelTransformer(new StringToFileTransformer());
        $builder->addEventSubscriber($this->dancerSubscriber);
        $builder->addEventSubscriber($this->profileSubscriber);
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults( array (
            'data_class' => 'DancePark\DancerBundle\Entity\Dancer',
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'dancer_profile';
    }
}