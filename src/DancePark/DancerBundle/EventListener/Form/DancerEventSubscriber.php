<?php
namespace DancePark\DancerBundle\EventListener\Form;

use FOS\UserBundle\Model\UserManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class DancerEventSubscriber implements  EventSubscriberInterface
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
        /**@var $data \DancePark\DancerBundle\Entity\Dancer*/
        $data = $eventData->getData();

        $logo = $data->getPhoto();
        if ($logo == null && $form->has('oldPhoto')) {
            $logo = $form->get('oldPhoto')->getData();
            $data->setPhoto($logo);
        }
    }
}