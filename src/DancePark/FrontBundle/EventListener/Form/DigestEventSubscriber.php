<?php
namespace DancePark\FrontBundle\EventListener\Form;

use DancePark\DancerBundle\Entity\Dancer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class DigestEventSubscriber implements EventSubscriberInterface
{
    protected $dancer;

    public function __construct($dancer = null) {
        $this->dancer = $dancer;
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

    public function postBind(FormEvent $event)
    {
        if (!is_null($this->dancer)) {
            $event->getForm()->getData()->setDancer($this->dancer);
        }
    }
}