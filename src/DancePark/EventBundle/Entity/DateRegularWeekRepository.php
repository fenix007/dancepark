<?php

namespace DancePark\EventBundle\Entity;

use DancePark\CommonBundle\Entity\DateRegularRepositoryInterface;
use Doctrine\ORM\EntityRepository;

/**
 * DateRegularWeekRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DateRegularWeekRepository extends EntityRepository implements DateRegularRepositoryInterface
{
    /**
     * @param $entityId integer
     * @param $list array
     * @return mixed
     */
    public function getDatesForEntityExcludeListed($entityId, $list)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $result = $qb
          ->select('d')
          ->from('EventBundle:DateRegularWeek', 'd')
          ->where('d.event = :orgId')
          ->setParameter('orgId', $entityId)
          ->andWhere($qb->expr()->notIn('d.id', $list))
          ->getQuery();
        return $result->getResult();
    }
}
