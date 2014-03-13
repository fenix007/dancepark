<?php
namespace DancePark\FrontBundle\Form\Type;

use DancePark\DancerBundle\Entity\Dancer;
use DancePark\DancerBundle\FOS\Model\UserManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class DancerSecretPasswordEventSubscriber implements  EventSubscriberInterface
{
    /** @var UserManager  */
    protected  $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }


    /**
     * {@inerhitDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::POST_BIND => 'postBind'
        );
    }

    /**
     *
     */
    public function postBind(FormEvent $event)
    {
        /** @var $data Dancer */
        $data = $event->getData();

        if ($plain = $data->getPlainSecretAnswer()) {
            $this->userManager->setSecretAnswer($data);
        }
    }
}