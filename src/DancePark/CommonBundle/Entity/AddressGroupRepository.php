<?php

namespace DancePark\CommonBundle\Entity;

use Doctrine\ORM\EntityNotFoundException;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

/**
 * AddressGroupRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AddressGroupRepository extends NestedTreeRepository
{
    /**
     * Find by name operator LIKE
     */
    public function findGroupByNameLike($pattern)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();

        $queryBuilder
            ->select('ms')
            ->from('CommonBundle:AddressGroup', 'ms')
            ->where('ms.name LIKE :name')
            //->andWhere('ms.parent = 2')
            ->setParameter('name', $pattern . '%')
            ->setMaxResults(20)
            ->addOrderBy('ms.name', 'ASC');
        try{
            $result = $queryBuilder->getQuery()->getResult();
        } catch (EntityNotFoundException $e) {
            $result = array();
        }

        return $result;
    }
}