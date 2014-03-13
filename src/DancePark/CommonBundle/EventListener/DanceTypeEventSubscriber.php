<?php
namespace DancePark\CommonBundle\EventListener;

use DancePark\CommonBundle\EventListener\Event\EntityEvent;

class DanceTypeEventSubscriber extends EntityAbstractEventSubscriber {

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'dance_type.pre_remove' => 'preRemove'
        );
    }

    /**
     * Pre remove callback
     */
    public function preRemove(EntityEvent $event)
    {
        $em = $this->doctrine->getEntityManager();
        $eventRepository = $em->getRepository('EventBundle:Event');
        if ($eventRepository->findByEventDanceType($event->getEntity())) {
            throw new \InvalidArgumentException(sprintf("Can't delete Dance Type, some events are exist."));
        }

        $dancerRepository = $em->getRepository('DancerBundle:Dancer');
        if ($dancerRepository->findByDancerDanceType($event->getEntity())) {
            throw new \InvalidArgumentException(sprintf("Can't delete Dance Type, some dancers are exist."));
        }


        $digestRepository = $em->getRepository('DancerBundle:Digest');
        $digests = $digestRepository->findBy(array('danceType' => $event->getEntity()));
        foreach ($digests as $digest) {
            $em->remove($digest);
        }

        $articleRepo = $em->getRepository('CommonBundle:Article');
        $articles = $articleRepo->findBy(array('danceType' => $event->getEntity()));
        if (count($articles) > 0) {
            foreach ($articles as $article) {
                $em->remove($article);
            }
        }
    }
}