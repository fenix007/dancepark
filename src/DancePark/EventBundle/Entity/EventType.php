<?php

namespace DancePark\EventBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EventType
 *
 * @ORM\Table(name="event_type")
 * @ORM\Entity(repositoryClass="DancePark\EventBundle\Entity\EventTypeRepository")
 */
class EventType
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="DancePark\EventBundle\Entity\EventTypeGroup")
     * @ORM\JoinColumn(name="event_type_group_id", nullable=false)
     */
    private $typeGroup;


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
     * Set name
     *
     * @param string $name
     * @return EventType
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set typeGroup
     *
     * @param integer $typeGroup
     * @return EventType
     */
    public function setTypeGroup($typeGroup)
    {
        $this->typeGroup = $typeGroup;
    
        return $this;
    }

    /**
     * Get typeGroup
     *
     * @return integer 
     */
    public function getTypeGroup()
    {
        return $this->typeGroup;
    }

    /**
     * Get string representation of class
     */
    public function __toString()
    {
        return $this->getName();
    }
}
