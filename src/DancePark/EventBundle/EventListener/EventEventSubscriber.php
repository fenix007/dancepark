<?php
namespace DancePark\EventBundle\EventListener;

use DancePark\CommonBundle\EventListener\EntityAbstractEventSubscriber;
use DancePark\CommonBundle\EventListener\Event\EntityEvent;
use DancePark\EventBundle\Entity\Event;
use DancePark\EventBundle\EventListener\Event\EventEvents;

class EventEventSubscriber extends  EntityAbstractEventSubscriber
{
    /**
     *  Pre remove callback
     */
    public function preRemove(EntityEvent $args)
    {
        if ($args->getEntity() instanceof Event) {
            $em = $this->doctrine->getEntityManager();
            $eventClosingRepository = $em->getRepository('EventBundle:EventClosing');
            $closings = $eventClosingRepository->findBy(array('event' => $args->getEntity()));
            foreach ($closings as $closed) {
                $em->remove($closed);
            }

            $digestRepository = $em->getRepository('DancerBundle:Digest');
            $digests = $digestRepository->findDigestsByEvent($args->getEntity());
            foreach ($digests as $digest) {
                $em->remove($digest);
            }

            $feedbackRepository = $em->getRepository('DancerBundle:Feedback');
            $feedbacks = $feedbackRepository->findBy(array('event' => $args->getEntity()));
            foreach ($feedbacks as $feedback) {
                $em->remove($feedback);
            }

            $dancerEventRepository = $em->getRepository('DancerBundle:DancerEvent');
            $events = $dancerEventRepository->findBy(array('event' => $args->getEntity()));
            foreach ($events as $ev) {
                $em->remove($ev);
            }

            $articleRepo = $em->getRepository('CommonBundle:Article');
            $articles = $articleRepo->findBy(array('event' => $args->getEntity()));
            if (count($articles) > 0) {
                foreach ($articles as $article) {
                    $em->remove($article);
                }
            }
        }

        $em->flush();
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            EventEvents::EVENT_PRE_REMOVE => 'preRemove'
        );
    }
}
