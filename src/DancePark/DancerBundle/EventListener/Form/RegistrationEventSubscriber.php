<?php
namespace DancePark\DancerBundle\EventListener\Form;

use DancePark\DancerBundle\Entity\Dancer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class RegistrationEventSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_BIND => 'preBindMock',
            FormEvents::POST_BIND => 'postBind',
        );
    }

    /**
     *
     */
    public function preBindMock(FormEvent $event)
    {
        $event->getForm()->getData()->setUsername('tmp');
    }

    public function postBind(FormEvent $event)
    {
        /** @var $data Dance */
        $data = $event->getForm()->getData();

        $data->setUsername($data->getEmail());
        $data->setUsernameCanonical($data->getEmailCanonical());
    }
}