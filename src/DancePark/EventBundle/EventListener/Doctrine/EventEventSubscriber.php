<?php
namespace DancePark\EventBundle\EventListener\Doctrine;

use DancePark\CommonBundle\EventListener\Event\EntityEvent;
use DancePark\DancerBundle\Entity\Digest;
use DancePark\DancerBundle\Entity\DigestRepository;
use DancePark\EventBundle\Entity\Event;
use DancePark\FrontBundle\Component\DigestManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventEventSubscriber implements  EventSubscriber
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     *
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        if ($args->getEntity() instanceof Event) {
            $digestManager = new DigestManager($args->getEntityManager());
            /** @var $digestRepo DigestRepository */
            $digestRepo = $args->getEntityManager()->getRepository('DancerBundle:Digest');
            $digestResult = array();

            /** @var $event Event */
            $event = $args->getEntity();
            if ($event->getPlace()) {
                $digestResult += $digestManager->findByAddressLike($event->getPlace()->getAddress(), $event);
            }

            foreach ($digestResult as $digest) {
                /** @var $digest Digest */
                $email = $digest->getEmail();
                if (!$email) {
                    $email = $digest->getDancer()->getEmail();
                }
                if ($email) {
                    $digestManager->sendMailEventTo($this->container, $email, $args->getEntity(), $digest);
                }
            }
        }
    }

    /**
     * PostLoad callback
     */
    public function postLoad(LifecycleEventArgs $args) {
        if ($args->getEntity() instanceof Event) {
            $args->getEntity()->setTeachers(str_replace("\\", '', $args->getEntity()->getTeachers()));
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
            'post_persist' => 'postPersist',
            'post_load' => 'postLoad'
        );
    }
}
