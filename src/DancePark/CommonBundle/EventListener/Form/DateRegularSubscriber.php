<?php
namespace DancePark\CommonBundle\EventListener\Form;

use DancePark\CommonBundle\Entity\DateRegularRepositoryInterface;
use DancePark\CommonBundle\EventListener\Event\EntityEvent;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class DateRegularSubscriber implements EventSubscriberInterface {
    /** @var $em EntityManager */
    protected $em;

    protected $fieldName;

    protected $entityName;

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::POST_BIND => 'postBind',
        );
    }

    public function __construct(Registry $doctrine) {
        $this->em = $doctrine->getManager();
    }

    public function setFieldName($fieldName) {
        $this->fieldName = $fieldName;
    }

    public function setEntityName($entityName) {
        $this->entityName = $entityName;
    }

    /**
     * @param EntityEvent $event
     */
    public function postBind(FormEvent $event) {
        $entity = $event->getForm()->getData();
        if ($this->entityName && $this->fieldName) {
            $getter = 'get' . ucfirst($this->fieldName);
            $repo = $this->em->getRepository($this->entityName);
            if (method_exists($entity, $getter) && $repo instanceof DateRegularRepositoryInterface) {
                $value = $entity->$getter();
                if (is_object($value) && $value instanceof PersistentCollection) {
                    $value = $value->toArray();
                    $ids = array();
                    foreach($value as $val) {
                        $id = $val->getId();
                        if ($id) {
                            $ids[] = $id;
                        }
                    }
                    $forRemove = $repo->getDatesForEntityExcludeListed($entity->getId(), $ids);
                    foreach($forRemove as $dateForRemove) {
                        $this->em->remove($dateForRemove);
                    }
                    $this->em->flush();
                }
            }
        }
    }
}