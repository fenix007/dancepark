<?php
namespace DancePark\CommonBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ExtendedDateType extends AbstractType {
    protected $startYear;

    protected $endYear;

    public function __construct($startYear = null, $endYear = null) {
        if (!$startYear) {
            $startYear = 1970;
        }
        if (!$endYear) {
            $endYear = date('Y');
        }
        $this->startYear = $startYear;
        $this->endYear = $endYear;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'years' => range($this->startYear, $this->endYear),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ex_date';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'date';
    }
}