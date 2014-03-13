<?php

namespace DancePark\EventBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EventLessonPrice
 *
 * @ORM\Table(name="event_lesson_price")
 * @ORM\Entity(repositoryClass="DancePark\EventBundle\Entity\EventLessonPriceRepository")
 */
class EventLessonPrice
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="DancePark\EventBundle\Entity\Event", inversedBy="lessonPrices", cascade={"all"})
     */
    private $event;

    /**
     * @var integer
     *
     * @ORM\Column(name="lesson", type="string", length=255)
     */
    private $lesson;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     */
    private $price;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set lesson
     *
     * @param integer $lesson
     * @return EventLessonPrice
     */
    public function setLesson($lesson)
    {
        $this->lesson = $lesson;
    
        return $this;
    }

    /**
     * Get lesson
     *
     * @return integer 
     */
    public function getLesson()
    {
        return $this->lesson;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return EventLessonPrice
     */
    public function setPrice($price)
    {
        $this->price = $price;
    
        return $this;
    }

    /**
     * Get price
     *
     * @return float 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set event
     *
     * @param \DancePark\EventBundle\Entity\Event $event
     * @return EventLessonPrice
     */
    public function setEvent(\DancePark\EventBundle\Entity\Event $event = null)
    {
        $this->event = $event;
    
        return $this;
    }

    /**
     * Get event
     *
     * @return \DancePark\EventBundle\Entity\Event 
     */
    public function getEvent()
    {
        return $this->event;
    }
}