<?php
namespace DancePark\CommonBundle\EventListener;

use DancePark\CommonBundle\Entity\MetroStation;
use DancePark\CommonBundle\EventListener\Event\CommonEvents;
use DancePark\CommonBundle\EventListener\Event\EntityEvent;

class MetroStationEventSubscriber extends EntityAbstractEventSubscriber
{

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            CommonEvents::METRO_STATION_PRE_REMOVE => 'preRemove',
        );
    }

    /**
     * Entity event
     * @param EntityEvent $event
     */
    public function preRemove(EntityEvent $event)
    {
        $em = $this->doctrine->getEntityManager();

        if ($event->getEntity() instanceof MetroStation) {
//        $placeRepository = $em->getRepository('CommonBundle:Place');
//        if ($placeRepository->findByMetro($event->getEntity())) {
//            throw new \InvalidArgumentException(sprintf("Can't delete Metro station, some Places are exist."));
//        }

        $digestRepository = $em->getRepository('DancerBundle:Digest');
        $digests = $digestRepository->findBy(array('metro' => $event->getEntity()));
        foreach ($digests as $digest) {
            $em->remove($digest);
        }
        }
    }


}