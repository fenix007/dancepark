<?php
namespace DancePark\FrontBundle\Component\EventManager\Filter\Type;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Request;

class RegionFilter extends AbstractFilter {
    protected $currentRegion;

    /**
     * Validate Request parameter data? if exists
     *
     * @param $data mixed
     * @return bool
     */
    public function validateParameter($data)
    {
        if (isset($data['region'])) {
             $this->currentRegion = $data['region'];
            return true;
        }
        return false;
    }

    /**
     * Provider filter action
     *
     * @param QueryBuilder $queryBuilder
     * @param mixed $data
     * @param Request $request
     *
     * @return mixed
     */
    public function applyFilter(QueryBuilder $queryBuilder, $data, Request $request = null)
    {
        $queryBuilder
            ->innerJoin('e.place', 'p', 'WITH')
            ->innerJoin('p.city_id', 'ag', 'WITH')
            ->andWhere('ag.region = :region')
            ->setParameter('region', $this->currentRegion);
    }

    /**
     * Add filter widget to the form while form building
     *
     * @param FormBuilder $formBuilder
     * @param Request $request
     * @return Form
     */
    public function editFilterForm(FormBuilder $formBuilder, Request $request = null)
    {
        $regionRepo = $this->em->getRepository('CommonBundle:AddressRegion');
        $regions = $regionRepo->findAll();

        $choices = array();

        foreach ($regions as $region) {
            $choices[$region->getId()] = $region->getName();
        }

        $formBuilder
            ->add('region', 'choice', array(
                'label' => 'label.region',
                'choices' => $choices,
                'required' => false,
            ));
    }
}