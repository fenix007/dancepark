<?php
namespace DancePark\CommonBundle\EventListener;

use DancePark\CommonBundle\Entity\AddressGroup;
use DancePark\CommonBundle\Entity\AddressGroupRepository;
use DancePark\CommonBundle\Entity\Place;
use DancePark\CommonBundle\EventListener\Event\CommonEvents;
use DancePark\CommonBundle\EventListener\Event\EntityEvent;
use Doctrine\ORM\EntityManager;

class PlaceEventSubscriber extends EntityAbstractEventSubscriber
{
    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            CommonEvents::PLACE_PRE_REMOVE => 'preRemove',
            CommonEvents::PLACE_PRE_PERSIST => 'prePersist',
            CommonEvents::PLACE_PRE_UPDATE => 'preUpdate',
        );
    }

    /**
     * Pre remove callback
     */
    public function preRemove(EntityEvent $event)
    {
        $em = $this->doctrine->getEntityManager();
        $eventRepository = $em->getRepository('EventBundle:Event');
        if ($eventRepository->findBy(array('place' => $event->getEntity()))) {
            throw new \InvalidArgumentException(sprintf("Can't delete Place, some events are exist."));
        }

        $digestRepository = $em->getRepository('DancerBundle:Digest');
        $digests = $digestRepository->findBy(array('place' => $event->getEntity()));
        foreach ($digests as $digest) {
            $em->remove($digest);
        }

        $articleRepo = $em->getRepository('CommonBundle:Article');
        $articles = $articleRepo->findBy(array('place' => $event->getEntity()));
        if (count($articles) > 0) {
            foreach ($articles as $article) {
                $em->remove($article);
            }
        }
    }

    public function setDefaults(EntityEvent $event)
    {
        $em = $this->getDoctrine()->getEntityManager();

        /** @var $entity Place */
        $entity = $event->getEntity();

        // Parse address
        $addressGroupRepo = $em->getRepository('CommonBundle:AddressGroup');
        $pieces = explode(', ', $entity->getAddress());
        if (count($pieces) > 2) {
            list($street, $number, $city) = $pieces;

            if ($cityObj = $addressGroupRepo->findOneBy(array('name' => $city, 'lvl' => 0))) {
                $entity->setCityId($cityObj);
                $street = substr($street, strpos($street, ' ') + 1);
                if ($streetObj = $addressGroupRepo->findOneBy(array('name' => $street, 'parent' => $cityObj->getId()))) {
                    $entity->setAddrGroup($streetObj);
                } else {
                    $entity->setAddrGroup(null);
                }
                $entity->setAddress($street . ', ' . $number);
            } else {
                $entity->setCityId(null);
                $entity->setAddrGroup(null);
                $entity->setAddress($city . ', ' . $street . ', ' . $number);
            }
        }

        // Check is valid data
        if (is_string($entity->getCityId())) {
            $city = $addressGroupRepo->findOneBy(array('name' => $entity->getCityId(), 'lvl' => 0));
            if (is_null($city)) {
                throw new \InvalidArgumentException(sprintf("Can't find city with name %s", $entity->getCityId()));
            } else {
                $entity->setCityId($city);
            }
        }
        if (is_string($entity->getAddrGroup())) {
            $addr = $addressGroupRepo->findOneBy(array('name' => $entity->getAddrGroup(), 'lvl' => 1));
            if (is_null($addr)) {
                throw new \InvalidArgumentException(sprintf("Can't find address with name %s", $entity->getAddrGroup()));
            } else {
                $entity->setAddrGroup($addr);
            }
        }
    }


    /**
     * Pre persist callback
     */
    public function prePersist(EntityEvent $event)
    {
        $this->setDefaults($event);
    }

    /**
     * Pre update callback
     */
    public function preUpdate(EntityEvent $event)
    {
        $this->setDefaults($event);
    }
}