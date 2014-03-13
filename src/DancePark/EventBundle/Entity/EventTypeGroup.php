<?php

namespace DancePark\EventBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EventTypeGroup
 *
 * @ORM\Table(name="event_type_group")
 * @ORM\Entity(repositoryClass="DancePark\EventBundle\Entity\EventTypeGroupRepository")
 */
class EventTypeGroup
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
     * @return EventTypeGroup
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
     * Get string representation of object
     */
    public function __toString()
    {
        return $this->getName();
    }
}
