<?php
namespace DancePark\CommonBundle\EventListener\Doctrine;

use DancePark\CommonBundle\Entity\Place;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class PlaceEventSubscriber implements EventSubscriber
{

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    function getSubscribedEvents()
    {
        return array(
            'post_load' => 'postLoad'
        );
    }

    /**
     *
     */
    public function postLoad(LifecycleEventArgs $args)
    {
//        $entity = $args->getEntity();
//
//        if ($entity instanceof Place) {
//             $addrGroupRepo = $args->getEntityManager()->getRepository("CommonBundle:AddressGroup");
//             $entity->setAddrGroup($addrGroupRepo->find(5));
//        }
    }
}