<?php
namespace DancePark\CommonBundle\EventListener;

use DancePark\CommonBundle\EventListener\Event\CommonEvents;
use DancePark\CommonBundle\EventListener\Event\EntityEvent;

class AddressLevelEventSubscriber extends EntityAbstractEventSubscriber {

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            CommonEvents::ADDRESS_LEVEL_PRE_REMOVE => 'preRemove',
        );
    }

    /**
     * Pre remove
     */
    public function preRemove(EntityEvent $event)
    {
        $em = $this->doctrine->getEntityManager();
        $arrdesRepo = $em->getRepository('CommonBundle:AddressGroup');
        if ($arrdesRepo->findBy(array('addressLevel' => $event->getEntity()))) {
            throw new \InvalidArgumentException(sprintf("Can't delete Address Level, some address groups are exist."));
        }

    }
}