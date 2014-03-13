<?php

namespace DancePark\OrganizationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Organization
 *
 * @ORM\Table(name="organization")
 * @ORM\Entity(repositoryClass="DancePark\OrganizationBundle\Entity\OrganizationRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Organization
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
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     * @Assert\Length(max=255, maxMessage="error.length255")
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="short_description", type="string", length=300, nullable=true)
     * @Assert\Length(max=300, maxMessage="error.length300")
     */
    private $shortDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_of_incorporation", type="date", nullable=true)
     * @Assert\Date(message="error.organization.date_of_incorporate")
     */
    private $dateOfIncorporation;

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
     * @var $places
     *
     * @ORM\ManyToMany(targetEntity="DancePark\CommonBundle\Entity\Place", mappedBy="organizations")
     */
    private $places;

    /**
     * @var $events
     *
     * @ORM\ManyToMany(targetEntity="DancePark\EventBundle\Entity\Event", mappedBy="organizations")
     */
    private $events;

    /**
     * @var $events
     *
     * @ORM\ManyToMany(targetEntity="DancePark\DancerBundle\Entity\Dancer", mappedBy="organizations")
     */
    private $dancers;

    /**
     * @var integer
     *
     * @ORM\OneToMany(
     *      targetEntity="DancePark\OrganizationBundle\Entity\DateRegularWeek",
     *      mappedBy="organization",
     *      cascade={"persist", "remove", "merge"}
     *  )
     */
    private $dateRegular;

    /**
     * @var array
     *
     * @ORM\Column(type="array", name="lesson_prices")
     */
    protected $lessonPrices;

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
     * @return Organization
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
     * @return Organization
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
     * Set type
     *
     * @param string $type
     * @return Organization
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set shortDescription
     *
     * @param string $shortDescription
     * @return Organization
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;
    
        return $this;
    }

    /**
     * Get shortDescription
     *
     * @return string 
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Organization
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set dateOfIncorporation
     *
     * @param \DateTime $dateOfIncorporation
     * @return Organization
     */
    public function setDateOfIncorporation($dateOfIncorporation)
    {
        $this->dateOfIncorporation = $dateOfIncorporation;
    
        return $this;
    }

    /**
     * Get dateOfIncorporation
     *
     * @return \DateTime 
     */
    public function getDateOfIncorporation()
    {
        return $this->dateOfIncorporation;
    }

    /**
     * Set webUrl
     *
     * @param string $webUrl
     * @return Organization
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
     * @return Organization
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
     * @return Organization
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
     * @return Organization
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
        return '/files/public/images/organization/logo';
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
     * Get string representation of object
     */
    public function __toString()
    {
        return $this->getName();
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->places = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add places
     *
     * @param \DancePark\CommonBundle\Entity\Place $places
     * @return Organization
     */
    public function addPlace(\DancePark\CommonBundle\Entity\Place $places)
    {
        $this->places[] = $places;
    
        return $this;
    }

    /**
     * Remove places
     *
     * @param \DancePark\CommonBundle\Entity\Place $places
     */
    public function removePlace(\DancePark\CommonBundle\Entity\Place $places)
    {
        $this->places->removeElement($places);
    }

    /**
     * Get places
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPlaces()
    {
        return $this->places;
    }

    /**
     * Add events
     *
     * @param \DancePark\EventBundle\Entity\Event $events
     * @return Organization
     */
    public function addEvent(\DancePark\EventBundle\Entity\Event $events)
    {
        $this->events[] = $events;
    
        return $this;
    }

    /**
     * Remove events
     *
     * @param \DancePark\EventBundle\Entity\Event $events
     */
    public function removeEvent(\DancePark\EventBundle\Entity\Event $events)
    {
        $this->events->removeElement($events);
    }

    /**
     * Get events
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Add dancers
     *
     * @param \DancePark\DancerBundle\Entity\Dancer $dancers
     * @return Organization
     */
    public function addDancer(\DancePark\DancerBundle\Entity\Dancer $dancers)
    {
        $this->dancers[] = $dancers;
    
        return $this;
    }

    /**
     * Remove dancers
     *
     * @param \DancePark\DancerBundle\Entity\Dancer $dancers
     */
    public function removeDancer(\DancePark\DancerBundle\Entity\Dancer $dancers)
    {
        $this->dancers->removeElement($dancers);
    }

    /**
     * Get dancers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDancers()
    {
        return $this->dancers;
    }

    /**
     * Add dateRegular
     *
     * @param \DancePark\OrganizationBundle\Entity\DateRegularWeek $dateRegular
     * @return Organization
     */
    public function addDateRegular(\DancePark\OrganizationBundle\Entity\DateRegularWeek $dateRegular)
    {
        $this->dateRegular[] = $dateRegular;
    
        return $this;
    }

    /**
     * Remove dateRegular
     *
     * @param \DancePark\OrganizationBundle\Entity\DateRegularWeek $dateRegular
     */
    public function removeDateRegular(\DancePark\OrganizationBundle\Entity\DateRegularWeek $dateRegular)
    {
        $this->dateRegular->removeElement($dateRegular);
    }

    /**
     * Get dateRegular
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDateRegular()
    {
        return $this->dateRegular;
    }

    /**
     * Set lessonPrices
     *
     * @param array $lessonPrices
     * @return Organization
     */
    public function setLessonPrices($lessonPrices)
    {
        $this->lessonPrices = $lessonPrices;
    
        return $this;
    }

    /**
     * Get lessonPrices
     *
     * @return array 
     */
    public function getLessonPrices()
    {
        return $this->lessonPrices;
    }

    /**
     * Get work time
     */
    public function getWorkTime()
    {
        $resultArray = array();
        if ($this->getDateRegular()->count() > 0) {
            $daysOfWeek = DateRegularWeek::getDaysOfWeek();
            foreach ($this->getDateRegular() as $lesson) {
                /** @var $lesson DateRegularWeek */
                $key = '';
                if ($lesson->getStartTime()) {
                    $key .= $lesson->getStartTime()->format('h:i');
                } else {
                    $key .= '...';
                }

                $key .= '-';

                if ($lesson->getEndTime()) {
                    $key .= $lesson->getEndTime()->format('h:i');
                } else {
                    $key .= '...';
                }
                if (!isset($resultArray[$key]) || (count($resultArray[$key]) > 1 && $lesson->getDayOfWeek() - $resultArray[$key][count($resultArray[$key]) - 1] != 1)) {
                    $resultArray[$key][] = $daysOfWeek[$lesson->getDayOfWeek()];
                } else {
                    if ($resultArray[$key][count($resultArray[$key]) - 2] == 0) {
                        unset($resultArray[$key][count($resultArray[$key]) - 2]);
                    }
                    $resultArray[$key][count($resultArray[$key]) - 1] = '-';
                    $resultArray[$key][] = $daysOfWeek[$lesson->getDayOfWeek()];
                }
            }
        }
        return $resultArray;
    }
}