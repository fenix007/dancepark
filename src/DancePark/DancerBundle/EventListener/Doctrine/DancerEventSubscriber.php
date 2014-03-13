<?php
namespace DancePark\DancerBundle\EventListener\Doctrine;

use DancePark\DancerBundle\Entity\Dancer;
use DancePark\EventBundle\Entity\Event;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DancerEventSubscriber implements EventSubscriber
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     *
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        if ($args->getEntity() instanceof Dancer) {
            /** @var $dancer Dancer */
            $dancer = $args->getEntity();
            if (!$dancer->getPhoto()) {
                $dancer->setPhoto('default/default_user.png');
            }
            if (!$dancer->getUsername() && $dancer->getEmail()) {
                $dancer->setUsername($dancer->getEmail());
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
            'pre_persist' => 'prePersist'
        );
    }
}
