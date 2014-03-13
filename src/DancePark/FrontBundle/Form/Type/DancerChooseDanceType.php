<?php
namespace DancePark\FrontBundle\Form\Type;

use DancePark\CommonBundle\Entity\DanceType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DancerChooseDanceType extends AbstractType
{
    protected  $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
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
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $danceTypes = $this->em->createQueryBuilder()->select('dt')->from('CommonBundle:DanceType', 'dt');
        $data = $options['data'];
        $defaultDanceTypesRaw = $data->getDanceType();
        $defaultDanceTypes = new ArrayCollection();
        if (!empty($defaultDanceTypesRaw)) {
            foreach($defaultDanceTypesRaw as $dt) {
                if (!$dt instanceof DanceType) {
                    $defaultDanceTypes[] = $dt->getDanceType();
                } else {
                    $defaultDanceTypes[] = $dt;
                }
            }
            $data->setDanceType($defaultDanceTypes);
        }
        $builder
            ->add('danceType', 'entity', array(
                'label' => 'label.danceType',
                'class' => 'DancePark\CommonBundle\Entity\DanceType',
                'multiple' => false,
                'query_builder' => $danceTypes
            ));
    }
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'dancer_dance_type';
    }
}