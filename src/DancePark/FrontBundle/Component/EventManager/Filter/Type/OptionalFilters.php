<?php
namespace DancePark\FrontBundle\Component\EventManager\Filter\Type;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Request;

class OptionalFilters extends AbstractFilter
{

    protected $children = false;

    protected $abonement = false;

    /**
     * Validate Request parameter data? if exists
     *
     * @param $data mixed
     * @return bool
     */
    public function validateParameter($data)
    {
        if (isset($data['children']) && $data['children']) {
            $this->children = true;
        }
        if (isset($data['abonement']) && $data['abonement']) {
            $this->abonement = true;
        }

        return $this->children || $this->abonement;
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
        if ($this->children) {
             $queryBuilder->andWhere('e.children = true');
        }
        if ($this->abonement) {
            $queryBuilder->andWhere('e.abonement = true');
        }
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
        $formBuilder->add('children', 'checkbox', array(
            'label' => 'label.children',
            'required' => false,
        ));
        $formBuilder->add('abonement', 'checkbox', array(
            'label' => 'label.abonement',
            'required' => false,
        ));
    }
}