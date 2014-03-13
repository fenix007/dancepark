<?php

namespace DancePark\CommonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Place
 *
 * @ORM\Table(name="place")
 * @ORM\Entity(repositoryClass="DancePark\CommonBundle\Entity\PlaceRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Place
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
     * @Assert\Length(max=255, maxMessage="error.length255")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="logo", type="string", length=255, nullable=true)
     * @Assert\Image()
     */
    private $logo;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="DancePark\CommonBundle\Entity\AddressGroup")
     * @ORM\JoinColumn(name="address_group_id")
     */
    private $addrGroup;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="DancePark\CommonBundle\Entity\AddressGroup")
     * @ORM\JoinColumn(name="city_id")
     */
    private $city_id;

    /**
     * @var integer
     *
     * @ORM\Column(type="string", name="address", length=255)
     * @Assert\Length(max=255, maxMessage="error.length255")
     */
    private $address;

    /**
     * @var integer
     *
     * @ORM\ManyToMany(targetEntity="DancePark\CommonBundle\Entity\MetroStation", cascade={"persist", "merge"})
     */
    private $metro;

    /**
     * @var float
     *
     * @ORM\Column(name="latitude", type="string", length=24, nullable=true)
     */
    private $latitude;

    /**
     * @var float
     *
     * @ORM\Column(name="longtitude", type="string", length=24, nullable=true)
     */
    private $longtitude;

    /**
     * @var string
     *
     * @ORM\Column(name="description_togo", type="string", length=300, nullable=true)
     * @Assert\Length(max=300, maxMessage="error.length300")
     */
    private $descriptionTogo;

    /**
     * @var string
     *
     * @ORM\Column(name="web_url", type="string", length=255, nullable=true)
     * @Assert\Length(max=255, maxMessage="error.length255")
     * @Assert\Url(message="error.url")
     */
    private $webUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="phone1", type="string", length=16, nullable=true)
     * @Assert\Length(max=16, maxMessage="error.phone_length")
     */
    private $phone1;

    /**
     * @var string
     *
     * @ORM\Column(name="phone2", type="string", length=16, nullable=true)
     * @Assert\Length(max=16, maxMessage="error.phone_length")
     */
    private $phone2;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     * @Assert\Length(max=255, maxMessage="error.length255")
     * @Assert\Email(message="error.email")
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="add_options", type="text", nullable=true)
     */
    private $addOptions;

    /**
     * @var string
     *
     * @ORM\Column(name="add_event", type="text", nullable=true)
     */
    private $addEvent;

    /**
     * @var
     *
     * @ORM\ManyToMany(targetEntity="DancePark\CommonBundle\Entity\DanceType")
     * @ORM\JoinTable(name="place_dance_type",
     *      joinColumns={@ORM\JoinColumn(name="place_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     *  )
     */
    private $danceType;

    /**
     * @var integer
     *
     * @ORM\ManyToMany(targetEntity="DancePark\OrganizationBundle\Entity\Organization", inversedBy="places")
     * @ORM\JoinTable(name="place_organization",
     *      joinColumns={@ORM\JoinColumn(name="place_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="organization_id", referencedColumnName="id")}
     *  )
     */
    private $organizations;

    /**
     * @var string
     *
     * @ORM\Column(name="how_to_get", type="array", nullable=true)
     */
    protected $howToGet;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->danceType = new \Doctrine\Common\Collections\ArrayCollection();
        $this->metro = new \Doctrine\Common\Collections\ArrayCollection();
        $this->howToGet = array();
    }

    /**
     * Add danceType
     *
     * @param \DancePark\CommonBundle\Entity\DanceType $danceType
     * @return Place
     */
    public function addDanceType(DanceType $danceType)
    {
        $this->danceType[] = $danceType;

        return $this;
    }

    /**
     * Remove danceType
     *
     * @param \DancePark\CommonBundle\Entity\DanceType $danceType
     */
    public function removeDanceType(DanceType $danceType)
    {
        $this->danceType->removeElement($danceType);
    }

    /**
     * Get danceType
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDanceType()
    {
        return $this->danceType;
    }

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
     * @return Place
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
     * Set logo
     *
     * @param string $logo
     * @return Place
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
    
        return $this;
    }

    /**
     * Get logo
     *
     * @return string 
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set addrGroup
     *
     * @param integer $addrGroup
     * @return Place
     */
    public function setAddrGroup($addrGroup)
    {
        $this->addrGroup = $addrGroup;
    
        return $this;
    }

    /**
     * Get addrGroup
     *
     * @return integer 
     */
    public function getAddrGroup()
    {
        return $this->addrGroup;
    }

    /**
     * Set address
     *
     * @param integer $address
     * @return Place
     */
    public function setAddress($address)
    {
        $this->address = $address;
    
        return $this;
    }

    /**
     * Get address
     *
     * @return integer 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     * @return Place
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
     * @param float $lolongtitude
     * @return Place
     */
    public function setLongtitude($lolongtitude)
    {
        $this->longtitude = $lolongtitude;
    
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
     * Set descriptionTogo
     *
     * @param string $descriptionTogo
     * @return Place
     */
    public function setDescriptionTogo($descriptionTogo)
    {
        $this->descriptionTogo = $descriptionTogo;
    
        return $this;
    }

    /**
     * Get descriptionTogo
     *
     * @return string 
     */
    public function getDescriptionTogo()
    {
        return $this->descriptionTogo;
    }

    /**
     * Set webUrl
     *
     * @param string $webUrl
     * @return Place
     */
    public function setWebUrl($webUrl)
    {
        $this->webUrl = $webUrl;
    
        return $this;
    }

    /**
     * Get webUrl
     *
     * @return string 
     */
    public function getWebUrl()
    {
        return $this->webUrl;
    }

    /**
     * Set phone1
     *
     * @param string $phone1
     * @return Place
     */
    public function setPhone1($phone1)
    {
        $this->phone1 = $phone1;
    
        return $this;
    }

    /**
     * Get phone1
     *
     * @return string 
     */
    public function getPhone1()
    {
        return $this->phone1;
    }

    /**
     * Set phone2
     *
     * @param string $phone2
     * @return Place
     */
    public function setPhone2($phone2)
    {
        $this->phone2 = $phone2;
    
        return $this;
    }

    /**
     * Get phone2
     *
     * @return string 
     */
    public function getPhone2()
    {
        return $this->phone2;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Place
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set addOptions
     *
     * @param string $addOptions
     * @return Place
     */
    public function setAddOptions($addOptions)
    {
        $this->addOptions = $addOptions;
    
        return $this;
    }

    /**
     * Get addOptions
     *
     * @return string 
     */
    public function getAddOptions()
    {
        return $this->addOptions;
    }

    /**
     * Set addEvent
     *
     * @param string $addEvent
     * @return Place
     */
    public function setAddEvent($addEvent)
    {
        $this->addEvent = $addEvent;
    
        return $this;
    }

    /**
     * Get addEvent
     *
     * @return string 
     */
    public function getAddEvent()
    {
        return $this->addEvent;
    }

    /**
     * @ORM\PrePersist
     *
     * Pre persist callback
     */
    public function prePersist()
    {
        $this->setDefaults();
    }

    /**
     * @ORM\PreUpdate
     *
     * Pre update callback
     */
    public function preUpdate()
    {
        $this->setDefaults();
    }

    /**
     * @ORM\PreRemove
     *
     * Pre remove callback
     */
    public function preRemove()
    {
        $this->deleteDefaults();
    }

    /**
     * Delete defaults
     */
    public function deleteDefaults()
    {
        if ($this->logo) {
            @unlink($this->getAbsolutePath());
        }
    }

    /**
     * Get absolute path in system
     *
     * @return string
     */
    public function getAbsolutePath()
    {
        return $this->logo === NULL ? NULL : $this->getUploadRootDir() . ($this->logo instanceof UploadedFile ? NULL :  '/' . $this->logo);
    }

    /**
     * Get web path
     *
     * @return string
     */
    public function getWebPath()
    {
        return $this->logo ? $this->getUploadDir() . '/' . $this->logo : NULL;
    }

    /**
     * Get upload root directory
     *
     * @return string
     */
    public function getUploadRootDir()
    {
        return __DIR__.'/../../../../web' . $this->getUploadDir();
    }

    /**
     * Get upload WEB directory
     *
     * @return string
     */
    public function getUploadDir()
    {
        return '/files/public/images/place/logo';
    }

    /**
     * Set defaults
     */
    public function setDefaults()
    {
        if (is_object($this->logo) && $this->logo instanceof UploadedFile) {
            // Generate path suffix
            $pathSuffix = date('Y/m/');
            // Generate file new name
            $fileNewName = 'b' . '_' . sha1(uniqid(mt_rand(), TRUE)) . '.' . $this->logo->guessExtension();

            // File moving
            $this->logo->move($this->getAbsolutePath() . '/' . $pathSuffix, $fileNewName);
            // Set base path
            $this->logo = $pathSuffix . $fileNewName;
        }
    }

    /**
     * Get formatted address
     */
    public function getFormattedAddress()
    {
        $address = '';

        /** @var $city AddressGroup */
        $city = $this->getCityId();

        if ($city) {
            $address = $city->getName();
        }

        $address .= $this->getAddress();

        return $address;
    }

    /**
     * Get String representation of object
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Add organizations
     *
     * @param \DancePark\OrganizationBundle\Entity\Organization $organizations
     * @return Place
     */
    public function addOrganization(\DancePark\OrganizationBundle\Entity\Organization $organizations)
    {
        $this->organizations[] = $organizations;
    
        return $this;
    }

    /**
     * Remove organizations
     *
     * @param \DancePark\OrganizationBundle\Entity\Organization $organizations
     */
    public function removeOrganization(\DancePark\OrganizationBundle\Entity\Organization $organizations)
    {
        $this->organizations->removeElement($organizations);
    }

    /**
     * Get organizations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOrganizations()
    {
        return $this->organizations;
    }

    /**
     * Set city_id
     *
     * @param \DancePark\CommonBundle\Entity\AddressGroup $cityId
     * @return Place
     */
    public function setCityId(\DancePark\CommonBundle\Entity\AddressGroup $cityId = null)
    {
        $this->city_id = $cityId;
    
        return $this;
    }

    /**
     * Get city_id
     *
     * @return \DancePark\CommonBundle\Entity\AddressGroup 
     */
    public function getCityId()
    {
        return $this->city_id;
    }

    /**
     * Set howToGet
     *
     * @param string $howToGet
     * @return Place
     */
    public function setHowToGet($howToGet)
    {
        $this->howToGet = $howToGet;
    
        return $this;
    }

    /**
     * Get howToGet
     *
     * @return string 
     */
    public function getHowToGet()
    {
        return $this->howToGet;
    }

    /**
     * Add path
     */
    public function addHowToGetPath($path) {
        $this->howToGet[] = $path;

        return $this;
    }

    /**
     *
     */
    public function setMetro(\Doctrine\Common\Collections\ArrayCollection $stations)
    {
        $this->metro = $stations;
    }

    /**
     * Add metro
     *
     * @param \DancePark\CommonBundle\Entity\MetroStation $metro
     * @return Place
     */
    public function addMetro(\DancePark\CommonBundle\Entity\MetroStation $metro)
    {
        $this->metro[] = $metro;
    
        return $this;
    }

    /**
     * Remove metro
     *
     * @param \DancePark\CommonBundle\Entity\MetroStation $metro
     */
    public function removeMetro(\DancePark\CommonBundle\Entity\MetroStation $metro)
    {
        $this->metro->removeElement($metro);
    }

    /**
     * Get metro
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMetro()
    {
        return $this->metro;
    }
}