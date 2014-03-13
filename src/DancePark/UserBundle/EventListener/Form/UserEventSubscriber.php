<?php
namespace DancePark\UserBundle\EventListener\Form;

use FOS\UserBundle\Model\UserManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class UserEventSubscriber implements  EventSubscriberInterface
{
    protected $um;

    /**
     * @param UserManager $um
     */
    public function __construct(UserManager $um)
    {
        $this->um = $um;
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

    public function postBind(FormEvent $eventData)
    {
        $form = $eventData->getForm();
        /**@var $data \DancePark\UserBundle\Entity\User*/
        $data = $eventData->getData();

        $username = $data->getUsername();
        if (!$username) {
            $data->setUsername($data->getEmail());
        }

        $this->um->updateCanonicalFields($data);
        if ($data->getPlainPassword()) {
            $this->um->updatePassword($data);
        }
    }
}