<?php
namespace DancePark\CommonBundle\EventListener;

use DancePark\CommonBundle\Entity\AddressGroup;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

/**
 * Class AddressGroupEventSubscriber
 * @package DancePark\CommonBundle\EventListener
 */
class AddressGroupEventSubscriber implements  EventSubscriber
{
    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof AddressGroup) {
            $repo = $args->getEntityManager()->getRepository('CommonBundle:AddressGroup');
//            $repo->verify();
//            $repo->recover();
            $args->getEntityManager()->flush();
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof AddressGroup) {
            $repo = $args->getEntityManager()->getRepository('CommonBundle:AddressGroup');
//            $repo->verify();
//            $repo->recover();
            $args->getEntityManager()->flush();
        }
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        if ($args->getEntity() instanceof AddressGroup) {
            $placeRepository = $args->getEntityManager()->getRepository('CommonBundle:Place');
            if ($placeRepository->findBy(array('addrGroup' => $args->getEntity()))) {
                throw new \InvalidArgumentException(sprintf("Can't delete Address Group, some Places are exist."));
            }
        }
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    function getSubscribedEvents()
    {
         return array(
             'postPersist',
             'postUpdate',
             'preRemove'
         );
    }
}