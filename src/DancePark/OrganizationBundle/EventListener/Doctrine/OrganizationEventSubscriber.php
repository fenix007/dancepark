<?php
namespace DancePark\OrganizationBundle\EventListener\Doctrine;

use DancePark\OrganizationBundle\Entity\Organization;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\EventArgs;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class OrganizationEventSubscriber implements EventSubscriber
{
    /**
     * {@inheritDoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            'preRemove' => 'preRemove',
        );
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $em = $args->getEntityManager();

        $entity = $args->getEntity();
        if ($entity instanceof Organization) {
            $placeRepo = $em->getRepository('CommonBundle:Place');
            if ($placeRepo->findByOrganization($entity)) {
                throw new \RuntimeException(sprintf("Can't delete entity, has relationship to Place type"));
            }

            $digestRepository = $em->getRepository('DancerBundle:Digest');
            $digests = $digestRepository->findBy(array('organization' => $args->getEntity()));
            foreach ($digests as $digest) {
                $em->remove($digest);
            }

            $feedbackRepository = $em->getRepository('DancerBundle:Feedback');
            $feedbacks = $feedbackRepository->findBy(array('organization' => $args->getEntity()));
            foreach ($feedbacks as $feedback) {
                $em->remove($feedback);
            }

            $articleRepo = $em->getRepository('CommonBundle:Article');
            $articles = $articleRepo->findBy(array('organization' => $args->getEntity()));
            if (count($articles) > 0) {
                foreach ($articles as $article) {
                    $em->remove($article);
                }
            }
        }
    }
}