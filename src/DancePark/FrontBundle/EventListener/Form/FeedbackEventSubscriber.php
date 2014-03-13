<?php
namespace DancePark\FrontBundle\EventListener\Form;

use DancePark\DancerBundle\Entity\Dancer;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class FeedbackEventSubscriber implements EventSubscriberInterface
{
    protected $manager;
    protected $dancer;
    protected $object;
    protected $type;

    public function __construct(Registry $doctrine, Dancer $dancer, $object, $type)
    {
        $this->manager = $doctrine->getManager();
        $this->dancer = $dancer;
        $this->object = $object;
        $this->type = $type;
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
        $form = $event->getForm();

        $data = $form->getData();

        $function = 'set' . ucfirst($this->type);
        if (method_exists($data, $function)) {
            $data->$function($this->object);
        }
        $data->setDancer($this->dancer);
    }
}