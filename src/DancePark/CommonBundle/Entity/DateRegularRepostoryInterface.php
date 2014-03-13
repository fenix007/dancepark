<?php

namespace DancePark\CommonBundle\Entity;

interface DateRegularRepositoryInterface
{
    /**
     * @param $entityId integer
     * @param $list array
     * @return mixed
     */
    public function getDatesForEntityExcludeListed($entityId, $list);
}