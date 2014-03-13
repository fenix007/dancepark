<?php
namespace DancePark\EventBundle\EventListener\Form;

use DancePark\EventBundle\Entity\DateRegularWeek;
use DancePark\EventBundle\Entity\Event;
use DancePark\EventBundle\Entity\EventLessonPrice;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class EventEventSubscriber implements EventSubscriberInterface
{
    protected $doctrine;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }
    /**
     * Post bind actions
     */
    public function postBind(FormEvent $event)
    {
        /**@var $data Event*/
        $data = $event->getData();

        // Set correct date
        $startTime = $data->getStartTime();
        if (is_string($startTime)) {
            $data->setStartTime(new \DateTime($startTime));
        }
        $endTime = $data->getEndTime();
        if (is_string($endTime)) {
            $data->setEndTime(new \DateTime($endTime));
        }

        /**@var $dateRegular DateRegularWeek*/
        foreach ($data->getDateRegular() as $dateRegular) {
            if (!$dateRegular->getId()) {
                $dateRegular->setEvent($data);
                $this->doctrine->getEntityManager()->persist($dateRegular);
            }
        }

        /**@var $lessonPrice EventLessonPrice*/
        foreach ($data->getLessonPrices() as $lessonPrice) {
             if (!$lessonPrice->getId()) {
                 $lessonPrice->setEvent($data);
                 $this->doctrine->getEntityManager()->persist($lessonPrice);
             }
        }

        // Set defaults
        $data->setCheckDate(new \DateTime());
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::POST_BIND => 'postBind',
        );
    }
}