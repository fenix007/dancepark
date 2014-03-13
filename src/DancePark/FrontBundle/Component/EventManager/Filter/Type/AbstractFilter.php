<?php
namespace DancePark\FrontBundle\Component\EventManager\Filter\Type;

use DancePark\FrontBundle\Component\EventManager\Filter\FilterInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractFilter implements FilterInterface
{

    /** @var $em EntityManager */
    protected $em;

    /**
     * Set object defaults
     *
     * @param Registry $doctrine
     */
    public function __construct(Registry $doctrine)
    {
        $this->em = $doctrine->getManager();
    }

    /**
     * Provider filter default action
     *
     * @param QueryBuilder $queryBuilder
     * @param Request $request
     *
     * @return mixed
     */
    public function applyEmpty(QueryBuilder $queryBuilder, Request $request = null)
    {
        return null;
    }

    /**
     * Provide functions impossible in SQL
     *
     * @return mixed
     */
    public function postExecuteFilter(array &$results, $filterData)
    {
        return null;
    }

    public function filterDateRegular(QueryBuilder $qb, EntityManager $em, &$first = true)
    {

    }

    /**
     * @param QueryBuilder $queryBuilder
     * @return bool
     */
    public static function checkJoin(QueryBuilder $queryBuilder, $aliasName)
    {
        $joins = $queryBuilder->getDQLPart('join');
        $joined = false;
        if (!empty($joins)) {
            foreach ($joins as $val) {
                foreach ($val as $join) {
                    /** @var $join Join */
                    if ($join->getAlias() == $aliasName) {
                        $joined = true;
                        break;
                    }
                }
                if ($joined) {
                    break;
                }
            }
            return $joined;
        }
        return $joined;
    }
}