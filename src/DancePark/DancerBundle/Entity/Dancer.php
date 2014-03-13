<?php

namespace DancePark\DancerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Dancer
 *
 * @ORM\Table(name="dancer")
 * @ORM\Entity(repositoryClass="DancePark\DancerBundle\Entity\DancerRepository") )
 * @ORM\HasLifecycleCallbacks()
 */
class Dancer extends User
{
    const DANCER_ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
    const DANCER_ROME_ADMIN = 'ROLE_ADMIN';
    const DANCER_MANAGER = 'ROLE_MANAGER';
    const DANCER_EVENT_MANAGER = 'ROLE_EVENT_MANAGER';
    const DANCER_ORGANIZATION_MANAGER = 'ROLE_ORGANIZATION_MANAGER';
    const DANCER_COMMON_MANAGER = 'ROLE_COMMON_MANAGER';
    const DANCER_USER = 'ROLE_USER';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected  $id;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
     * @Assert\Length(max=255, maxMessage="error.length255")
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     * @Assert\Length(max=255, maxMessage="error.length255")
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="photo", type="string", length=255, nullable=true)
     * @Assert\Image()
     */
    private $photo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthday", type="datetime", nullable=true)
     * @Assert\Date(message="error.date");
     */
    private $birthday;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_to_dance", type="datetime", nullable=true)
     * @Assert\Date(message="error.date");
     */
    private $startToDance;

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
     * @ORM\Column(name="biography", type="text", nullable=true)
     */
    private $biography;

    /**
     * @var string
     *
     * @ORM\Column(name="short_overview", type="string", length=300, nullable=true)
     * @Assert\Length(max=300, maxMessage="error.length300")
     */
    private $shortOverview;

    /**
     * @var string
     *
     * @ORM\Column(name="secret_question", type="string", length=255, nullable=true)
     * @Assert\Length(max=255, maxMessage="error.length255")
     */
    private $secretQuestion;

    /**
     * @var string
     */
    protected $plainSecretAnswer;

    /**
     * @var string
     *
     * @ORM\Column(name="secret_answer", type="string", length=255, nullable=true)
     * @Assert\Length(max=255, maxMessage="error.length255")
     */
    private $secretAnswer;

    /**
     * @var $danceTypes
     *
     * @ORM\OneToMany(targetEntity="DancePark\DancerBundle\Entity\DancerDanceType", mappedBy="dancer", cascade={"all"})
     */
    private $danceType;

    /**
     * @var integer
     *
     * @ORM\ManyToMany(targetEntity="DancePark\OrganizationBundle\Entity\Organization", inversedBy="dancers")
     * @ORM\JoinTable(name="dancer_organization",
     *      joinColumns={@ORM\JoinColumn(name="place_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="organization_id", referencedColumnName="id")}
     *  )
     */
    private $organizations;

    /**
     * @var booleam
     *
     * @ORM\Column(name="is_pro", type="boolean", nullable=true)
     */
    private $isPro;

    /**
     * @var boolean
     *
     * @ORM\Column(name="find_partner", type="boolean", nullable=true)
     */
    private $findPartner;

    /**
     * @var float
     *
     * @ORM\Column(name="weight", type="float", nullable=true)
     */
    private $weight;

    /**
     * @var float
     *
     * @ORM\Column(name="height", type="float", nullable=true)
     */
    private $height;

    /**
     * @var string
     *
     * @ORM\Column(name="dance_club", type="string", length=255, nullable=true)
     * @Assert\Length(max=255, maxMessage="error.length255")
     */
    private $danceClub;

    /**
     * Construct object
     */
    public function __construct()
    {
          parent::__construct();
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
     * Set firstName
     *
     * @param string $firstName
     * @return Dancer
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    
        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return Dancer
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    
        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set photo
     *
     * @param string $photo
     * @return Dancer
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;
    
        return $this;
    }

    /**
     * Get photo
     *
     * @return string 
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Set birthday
     *
     * @param \DateTime $birthday
     * @return Dancer
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    
        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime 
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set phone1
     *
     * @param string $phone1
     * @return Dancer
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
     * @return Dancer
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
     * Set biography
     *
     * @param string $biography
     * @return Dancer
     */
    public function setBiography($biography)
    {
        $this->biography = $biography;
    
        return $this;
    }

    /**
     * Get biography
     *
     * @return string 
     */
    public function getBiography()
    {
        return $this->biography;
    }

    /**
     * Set shortOverview
     *
     * @param string $shortOverview
     * @return Dancer
     */
    public function setShortOverview($shortOverview)
    {
        $this->shortOverview = $shortOverview;
    
        return $this;
    }

    /**
     * Get shortOverview
     *
     * @return string 
     */
    public function getShortOverview()
    {
        return $this->shortOverview;
    }

    /**
     * Set secretQuestion
     *
     * @param string $secretQuestion
     * @return Dancer
     */
    public function setSecretQuestion($secretQuestion)
    {
        $this->secretQuestion = $secretQuestion;
    
        return $this;
    }

    /**
     * Get secretQuestion
     *
     * @return string 
     */
    public function getSecretQuestion()
    {
        return $this->secretQuestion;
    }

    /**
     * Set plain secret answer
     *
     * @param string $plainSecretAnswer
     */
    public function setPlainSecretAnswer($plainSecretAnswer)
    {
        $this->plainSecretAnswer = $plainSecretAnswer;
    }

    /**
     * Get plain secret answer
     *
     * @return mixed
     */
    public function getPlainSecretAnswer()
    {
        return $this->plainSecretAnswer;
    }



    /**
     * Set secretAnswer
     *
     * @param string $secretAnswer
     * @return Dancer
     */
    public function setSecretAnswer($secretAnswer)
    {
        $this->secretAnswer = $secretAnswer;
    
        return $this;
    }

    /**
     * Get secretAnswer
     *
     * @return string 
     */
    public function getSecretAnswer()
    {
        return $this->secretAnswer;
    }

    /**
     * Add danceType
     *
     * @param $danceType
     * @return Dancer
     */
    public function addDanceType($danceType)
    {
        $this->danceType[] = $danceType;

        return $this;
    }

    /**
     * Remove danceType
     *
     * @param $danceType
     */
    public function removeDanceType($danceType)
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
     * Set Dance types
     *
     * @return Dancer
     */
    public function setDanceType($danceTypes)
    {
        $this->danceType = $danceTypes;

        return $this;
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
        if ($this->photo) {
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
        return $this->photo === NULL ? NULL : $this->getUploadRootDir() . ($this->photo instanceof UploadedFile ? NULL :  '/' . $this->photo);
    }

    /**
     * Get web path
     *
     * @return string
     */
    public function getWebPath()
    {
        $photo = $this->photo;
        if (is_object($photo)) {
            $photo = $this->photo;
            $uploadRootDir = $this->getUploadRootDir();
            $photo = substr($photo, strpos($photo, $uploadRootDir) + strlen($uploadRootDir));
        }
        return $this->photo ? $this->getUploadDir() . '/' . $photo : NULL;
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
        return '/files/public/images/dancer/logo';
    }

    /**
     * Set defaults
     */
    public function setDefaults()
    {
        if (is_object($this->photo) && $this->photo instanceof UploadedFile) {
            // Generate path suffix
            $pathSuffix = date('Y/m/');
            // Generate file new name
            $fileNewName = 'b' . '_' . sha1(uniqid(mt_rand(), TRUE)) . '.' . $this->photo->guessExtension();

            // File moving
            $this->photo->move($this->getAbsolutePath() . '/' . $pathSuffix, $fileNewName);
            // Set base path
            $this->photo = $pathSuffix . $fileNewName;
        }
    }

    /**
     * Get string representation of object
     */
    public function __toString()
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    /**
     * Add organizations
     *
     * @param \DancePark\OrganizationBundle\Entity\Organization $organizations
     * @return Dancer
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
     * Set isPro
     *
     * @param boolean $isPro
     * @return Dancer
     */
    public function setIsPro($isPro)
    {
        $this->isPro = $isPro;
    
        return $this;
    }

    /**
     * Get isPro
     *
     * @return boolean 
     */
    public function getIsPro()
    {
        return $this->isPro;
    }

    /**
     * Set findPartner
     *
     * @param boolean $findPartner
     * @return Dancer
     */
    public function setFindPartner($findPartner)
    {
        $this->findPartner = $findPartner;
    
        return $this;
    }

    /**
     * Get findPartner
     *
     * @return boolean 
     */
    public function getFindPartner()
    {
        return $this->findPartner;
    }

    /**
     * Set weight
     *
     * @param float $weight
     * @return Dancer
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    
        return $this;
    }

    /**
     * Get weight
     *
     * @return float 
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set height
     *
     * @param float $height
     * @return Dancer
     */
    public function setHeight($height)
    {
        $this->height = $height;
    
        return $this;
    }

    /**
     * Get height
     *
     * @return float 
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     *
     */
    public static function getDancerRoles($keysOnly = false)
    {
        $roles = array(
            static::DANCER_USER => 'label.dancer.role_user',
            static::DANCER_COMMON_MANAGER => 'label.dancer.common_manager',
            static::DANCER_EVENT_MANAGER => 'label.dancer.event_manager',
            static::DANCER_ORGANIZATION_MANAGER => 'label.dancer.organization',
            static::DANCER_MANAGER => 'label.dancer.manager',
            static::DANCER_ROME_ADMIN => 'label.dancer.admin',
            static::DANCER_ROLE_SUPER_ADMIN => 'label.dancer.super_admin',
        );
        if ($keysOnly) {
            return array_keys($roles);
        }
        return $roles;
    }

    /**
     * Set danceClub
     *
     * @param string $danceClub
     * @return Dancer
     */
    public function setDanceClub($danceClub)
    {
        $this->danceClub = $danceClub;
    
        return $this;
    }

    /**
     * Get danceClub
     *
     * @return string 
     */
    public function getDanceClub()
    {
        return $this->danceClub;
    }

    /**
     * Set startToDance
     *
     * @param \DateTime $startToDance
     * @return Dancer
     */
    public function setStartToDance($startToDance)
    {
        $this->startToDance = $startToDance;
    
        return $this;
    }

    /**
     * Get startToDance
     *
     * @return \DateTime 
     */
    public function getStartToDance()
    {
        return $this->startToDance;
    }
}