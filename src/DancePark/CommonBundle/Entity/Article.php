<?php

namespace DancePark\CommonBundle\Entity;

use DancePark\OrganizationBundle\Entity\Organization;
use Doctrine\ORM\Mapping as ORM;

/**
 * Article
 *
 * @ORM\Table(name="article")
 * @ORM\Entity(repositoryClass="DancePark\CommonBundle\Entity\ArticleRepository")
 */
class Article
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text")
     */
    private $body;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="DancePark\OrganizationBundle\Entity\Organization")
     */
    private $organization;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="DancePark\EventBundle\Entity\Event")
     */
    private $event;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="DancePark\CommonBundle\Entity\Place")
     */
    private $place;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="DancePark\CommonBundle\Entity\DanceType")
     */
    private $danceType;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="DancePark\DancerBundle\Entity\Dancer", cascade={"all"})
     */
    private $author;


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
     * Set title
     *
     * @param string $title
     * @return Article
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set body
     *
     * @param string $body
     * @return Article
     */
    public function setBody($body)
    {
        $this->body = $body;
    
        return $this;
    }

    /**
     * Get body
     *
     * @return string 
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set organization
     *
     * @param \DancePark\OrganizationBundle\Entity\Organization $organization
     * @return Article
     */
    public function setOrganization($organization = null)
    {
        $this->organization = $organization;
    
        return $this;
    }

    /**
     * Get organization
     *
     * @return \DancePark\OrganizationBundle\Entity\Organization 
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Set event
     *
     * @param \DancePark\EventBundle\Entity\Event $event
     * @return Article
     */
    public function setEvent($event = null)
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

    /**
     * Set place
     *
     * @param \DancePark\CommonBundle\Entity\Place $place
     * @return Article
     */
    public function setPlace($place = null)
    {
        $this->place = $place;
    
        return $this;
    }

    /**
     * Get place
     *
     * @return \DancePark\CommonBundle\Entity\Place 
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Set danceType
     *
     * @param \DancePark\CommonBundle\Entity\DanceType $danceType
     * @return Article
     */
    public function setDanceType($danceType = null)
    {
        $this->danceType = $danceType;
    
        return $this;
    }

    /**
     * Get danceType
     *
     * @return \DancePark\CommonBundle\Entity\DanceType 
     */
    public function getDanceType()
    {
        return $this->danceType;
    }

    /**
     * Set author
     *
     * @param \DancePark\DancerBundle\Entity\Dancer $author
     * @return Article
     */
    public function setAuthor($author = null)
    {
        $this->author = $author;
    
        return $this;
    }

    /**
     * Get author
     *
     * @return \DancePark\DancerBundle\Entity\Dancer 
     */
    public function getAuthor()
    {
        return $this->author;
    }
}