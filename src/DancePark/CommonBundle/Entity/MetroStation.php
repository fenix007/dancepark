<?php

namespace DancePark\CommonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MetroStation
 *
 * @ORM\Table(name="metro_station")
 * @ORM\Entity(repositoryClass="DancePark\CommonBundle\Entity\MetroStationRepository")
 */
class MetroStation
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
     * @ORM\Column(name="name", type="string", length=24)
     */
    private $name;

    /**
     * @var float
     *
     * @ORM\Column(name="latitude", type="string", length=24)
     */
    private $latitude;

    /**
     * @var float
     *
     * @ORM\Column(name="longtitude", type="string", length=24)
     */
    private $longtitude;

    /**
     * @var string
     *
     * @ORM\Column(name="google_id", type="string", length=255, nullable=true)
     */
    private $googleId;


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
     * @return MetroStation
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
     * Set latitude
     *
     * @param float $latitude
     * @return MetroStation
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    
        return $this;
    }

    /**
     * Get latitude
     *
     * @return float 
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longtitude
     *
     * @param float $longtitude
     * @return MetroStation
     */
    public function setLongtitude($longtitude)
    {
        $this->longtitude = $longtitude;
    
        return $this;
    }

    /**
     * Get longtitude
     *
     * @return float 
     */
    public function getLongtitude()
    {
        return $this->longtitude;
    }

    /**
     * Get string representation of object
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Set googleId
     *
     * @param string $googleId
     * @return MetroStation
     */
    public function setGoogleId($googleId)
    {
        $this->googleId = $googleId;
    
        return $this;
    }

    /**
     * Get googleId
     *
     * @return string 
     */
    public function getGoogleId()
    {
        return $this->googleId;
    }
}