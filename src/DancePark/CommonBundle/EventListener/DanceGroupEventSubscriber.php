<?php
namespace DancePark\CommonBundle\EventListener;

use DancePark\CommonBundle\EventListener\Event\EntityEvent;
use DancePark\CommonBundle\EventListener\EntityAbstractEventSubscriber;

class DanceGroupEventSubscriber extends EntityAbstractEventSubscriber
{

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'dance_group.pre_remove' => 'preRemove'
        );
    }

    /**
     * {@inheritDoc}
     */
    public function preRemove(EntityEvent $event)
    {
        $em = $this->doctrine->getEntityManager();
        $eventRepository = $em->getRepository('CommonBundle:DanceType');
        if ($eventRepository->findBy(array('danceGroup' => $event->getEntity()))) {
            throw new \InvalidArgumentException(sprintf("Can't delete Dance Group, some dance types are exist."));
        }
    }
}