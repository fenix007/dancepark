<?php

namespace DancePark\DancerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DancerEvent
 *
 * @ORM\Table(name="dancer_event")
 * @ORM\Entity(repositoryClass="DancePark\DancerBundle\Entity\DancerEventRepository")
 */
class DancerEvent
{
    const DANCER_STATUS_WILL = 'label.will';
    const DANCER_STATUS_MAYBE = 'label.maybe';

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
     * @ORM\ManyToOne(targetEntity="DancePark\DancerBundle\Entity\Dancer")
     */
    private $dancer;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="DancePark\EventBundle\Entity\Event")
     */
    private $event;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     * @Assert\Date(message="error.date")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=300, nullable=true)
     * @Assert\Length(max=300, maxMessage="error.length300")
     */
    private $status;


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
     * Set dancer
     *
     * @param integer $dancer
     * @return DancerEvent
     */
    public function setDancer($dancer)
    {
        $this->dancer = $dancer;
    
        return $this;
    }

    /**
     * Get dancer
     *
     * @return integer 
     */
    public function getDancer()
    {
        return $this->dancer;
    }

    /**
     * Set event
     *
     * @param integer $event
     * @return DancerEvent
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
     * Set date
     *
     * @param \DateTime $date
     * @return DancerEvent
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return DancerEvent
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }
}
