<?php

namespace DancePark\DancerBundle\EventListener\Form;

use DancePark\DancerBundle\Entity\Dancer;
use DancePark\DancerBundle\Entity\DancerDanceType;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class DancerDanceTypeEventSubscriber implements EventSubscriberInterface {
    /** @var $em EntityManager */
    protected $em;
    public function __construct(Registry $doctrine) {
        $this->em = $doctrine->getManager();
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::POST_BIND => 'postBind'
        );
    }

    public function postBind(FormEvent $formEvent) {
        /** @var $data Dancer */
        $data = $formEvent->getData();
        $danceTypes = $data->getDanceType();
        $danceTypeIds = array();
        $ddts = array();
        foreach($danceTypes as $danceType) {
            $danceTypeIds[] = $danceType->getId();
            $dancerDanceType = new DancerDanceType();
            $dancerDanceType->setDancer($data);
            $dancerDanceType->setDanceType($danceType);
            $dancerDanceType->setIsPro(false);
            $ddts[] = $dancerDanceType;
        }
        $data->setDanceType($ddts);
        if ($data->getId()) {
            $ddtRepo = $this->em->getRepository('DancerBundle:DancerDanceType');
            $forRemove = $ddtRepo->getTypesForEntityExcludeListed($data->getId(), $danceTypeIds);
            foreach ($forRemove as $entity) {
                $this->em->remove($entity);
            }
            $this->em->flush();
        }
    }
}