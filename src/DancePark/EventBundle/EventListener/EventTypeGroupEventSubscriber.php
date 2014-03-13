<?php
namespace DancePark\EventBundle\EventListener;

use DancePark\CommonBundle\EventListener\EntityAbstractEventSubscriber;
use DancePark\CommonBundle\EventListener\Event\EntityEvent;
use DancePark\EventBundle\EventListener\Event\EventEvents;

class EventTypeGroupEventSubscriber extends EntityAbstractEventSubscriber
{

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            EventEvents::EVENT_TYPE_GROUP_PRE_REMOVE => 'preRemove'
        );
    }

    /**
     * @param EntityEvent $event
     */
    public function preRemove(EntityEvent $event)
    {
        $em = $this->doctrine->getEntityManager();
        $eventRepository = $em->getRepository('EventBundle:EventType');
        if ($eventRepository->findBy(array('typeGroup' => $event->getEntity()))) {
            throw new \InvalidArgumentException(sprintf("Can't delete Event Type Group, some event types are exist."));
        }
    }
}