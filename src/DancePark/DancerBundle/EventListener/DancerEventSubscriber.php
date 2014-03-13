<?php
namespace DancePark\DancerBundle\EventListener;

use DancePark\CommonBundle\EventListener\EntityAbstractEventSubscriber;
use DancePark\CommonBundle\EventListener\Event\EntityEvent;
use DancePark\DancerBundle\Entity\Dancer;
use DancePark\EventBundle\Entity\Event;

class DancerEventSubscriber extends  EntityAbstractEventSubscriber
{
    /**
     *  Pre remove callback
     */
    public function preRemove(EntityEvent $args)
    {
        if ($args->getEntity() instanceof Dancer) {
            $em = $this->doctrine->getManager();

            $dancerDanceTypeRepo = $em->getRepository('DancerBundle:DancerDanceType');
            $types = $dancerDanceTypeRepo->findBy(array('dancer' => $args->getEntity()));
            foreach($types as $type) {
                $em->remove($type);
            }

            $dancerEventRepo = $em->getRepository('DancerBundle:DancerEvent');
            $closings = $dancerEventRepo->findBy(array('dancer' => $args->getEntity()));
            foreach ($closings as $closed) {
                $em->remove($closed);
            }

            $digestRepository = $em->getRepository('DancerBundle:Digest');
            $digests = $digestRepository->findBy(array('dancer' => $args->getEntity()));
            foreach ($digests as $digest) {
                $em->remove($digest);
            }

            $feedbackRepository = $em->getRepository('DancerBundle:Feedback');
            $feedbacks = $feedbackRepository->findBy(array('dancer' => $args->getEntity()));
            foreach ($feedbacks as $feedback) {
                $em->remove($feedback);
            }

            $articleRepo = $em->getRepository('CommonBundle:Article');
            $articles = $articleRepo->findBy(array('author' => $args->getEntity()));
            if (count($articles) > 0) {
                foreach ($articles as $article) {
                    $em->remove($article);
                }
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'dancer.pre_remove' => 'preRemove'
        );
    }
}
