<?php
namespace DancePark\CommonBundle\EventListener;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class EntityAbstractEventSubscriber implements EventSubscriberInterface
{
    /** @var $doctrine Registry */
    protected $doctrine;

    function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function setDoctrine($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getDoctrine()
    {
        return $this->doctrine;
    }
}