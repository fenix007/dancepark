<?php
namespace DancePark\CommonBundle\EventListener\Event;

use Symfony\Component\EventDispatcher\Event;

class EntityEvent extends Event
{
    protected $entity;

    protected $checkFields;

    /**
     * Set event entity
     * @param $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Get event entity
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param $checkFields
     */
    public function setCheckFields($checkFields)
    {
        $this->checkFields = $checkFields;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCheckFields()
    {
        return $this->checkFields;
    }
}