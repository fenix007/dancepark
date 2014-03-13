<?php
namespace DancePark\EventBundle\EventListener;

use DancePark\CommonBundle\EventListener\EntityAbstractEventSubscriber;
use DancePark\CommonBundle\EventListener\Event\EntityEvent;
use DancePark\EventBundle\EventListener\Event\EventEvents;

class EventTypeEventSubscriber extends EntityAbstractEventSubscriber
{

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            EventEvents::EVENT_TYPE_PRE_REMOVE => 'preRemove'
        );
    }

    /**
     * @param EntityEvent $event
     */
    public function preRemove(EntityEvent $event)
    {
        $em = $this->doctrine->getEntityManager();
        $eventRepository = $em->getRepository('EventBundle:Event');
        if ($eventRepository->findBy(array('type' => $event->getEntity()))) {
            throw new \InvalidArgumentException(sprintf("Can't delete Event Type, some events are exist."));
        }
    }
}