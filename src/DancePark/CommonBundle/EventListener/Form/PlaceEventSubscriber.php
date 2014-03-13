<?php
namespace DancePark\CommonBundle\EventListener\Form;

use DancePark\CommonBundle\Entity\Place;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class PlaceEventSubscriber implements EventSubscriberInterface
{

    /**
     * {@innheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::POST_BIND => 'postBind',
        );
    }

    /**
     * @param FormEvent $event
     */
    public function postBind(FormEvent $event)
    {
        $form = $event->getForm();

        /** @var $data Place */
        $data = $form->getData();

        $logo = $data->getLogo();
        if ($logo == null) {
            if ($form->has('oldLogo')) {
                $logo = $form->get('oldLogo')->getData();
                $data->setLogo($logo);
            } else {
                $data->setLogo(null);
            }
        }

        $event->getDispatcher()->dispatch('place.change', $event);
        //$this->generateMetroStation($data);
    }

    /**
     * Generate metro station
     *
     * @param $data Place
     */
    public function generateMetroStation($data) {

    }
}