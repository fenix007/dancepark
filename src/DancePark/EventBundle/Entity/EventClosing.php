<?php

namespace DancePark\EventBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EventClosing
 *
 * @ORM\Table(name="event_closing")
 * @ORM\Entity(repositoryClass="DancePark\EventBundle\Entity\EventClosingRepository")
 */
class EventClosing
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
     * @ORM\OneToOne(targetEntity="DancePark\EventBundle\Entity\Event", cascade={"remove"})
     */
    private $event;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="begin", type="datetime")
     * @Assert\DateTime(message="error.event_closing.begin")
     */
    private $begin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end", type="datetime")
     * @Assert\DateTime(message="error.event_closing.end")
     */
    private $end;

    /**
     * @var string
     *
     * @ORM\Column(name="reason", type="string", length=500)
     * @Assert\Length(max="500", maxMessage="error.event_closing.reason")
     */
    private $reason;


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
     * Set event
     *
     * @param integer $event
     * @return EventClosing
     */
    public function setEvent($event)
    {
        $this->event = $event;
    
        return $this;
    }

    /**
     * Get event
     *
     * @return integer 
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set begin
     *
     * @param \DateTime $begin
     * @return EventClosing
     */
    public function setBegin($begin)
    {
        $this->begin = $begin;
    
        return $this;
    }

    /**
     * Get begin
     *
     * @return \DateTime 
     */
    public function getBegin()
    {
        return $this->begin;
    }

    /**
     * Set end
     *
     * @param \DateTime $end
     * @return EventClosing
     */
    public function setEnd($end)
    {
        $this->end = $end;
    
        return $this;
    }

    /**
     * Get end
     *
     * @return \DateTime 
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Set reason
     *
     * @param string $reason
     * @return EventClosing
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
    
        return $this;
    }

    /**
     * Get reason
     *
     * @return string 
     */
    public function getReason()
    {
        return $this->reason;
    }
}
