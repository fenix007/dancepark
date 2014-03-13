<?php
namespace DancePark\FrontBundle\Component\EventManager\Filter;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface for front-page filters
 *
 * Class FilterInterface
 * @package DancePark\FrontBundle\Component\EventManager\Filter
 */
interface FilterInterface
{
    const PAGE_COUNT_RESULT = 6;
    /**
     * Validate Request parameter data? if exists
     *
     * @param $data mixed
     * @return bool
     */
    public function validateParameter($data);

    /**
     * Provider filter action
     *
     * @param QueryBuilder $queryBuilder
     * @param mixed $data
     * @param Request $request
     *
     * @return mixed
     */
    public function applyFilter(QueryBuilder $queryBuilder, $data, Request $request = null);

    /**
     * Provider filter default action
     *
     * @param QueryBuilder $queryBuilder
     * @param Request $request
     *
     * @return mixed
     */
    public function applyEmpty(QueryBuilder $queryBuilder, Request $request = null);

    /**
     * Provide functions impossible in SQL
     *
     * @return mixed
     */
    public function postExecuteFilter(array &$results, $filterData);

    /**
     * Add filter widget to the form while form building
     *
     * @param FormBuilder $formBuilder
     * @param Request $request
     * @return Form
     */
    public function editFilterForm(FormBuilder $formBuilder, Request $request = null);
}