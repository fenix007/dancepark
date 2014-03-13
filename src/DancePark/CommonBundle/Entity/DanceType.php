<?php

namespace DancePark\CommonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints  as Assert;
use Symfony\Component\Validator\ExecutionContext;

/**
 * DanceType
 *
 * @ORM\Table(name="dance_type")
 * @ORM\Entity(repositoryClass="DancePark\CommonBundle\Entity\DanceTypeRepository")
 * @Assert\Callback(methods={"validateKind"})
 */
class DanceType
{
    const DANCE_TYPE_KIND_SOLO = 1;
    const DANCE_TYPE_KIND_COUPLE = 2;

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
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="DancePark\CommonBundle\Entity\DanceGroup")
     * @ORM\JoinColumn(name="dance_group_id")
     */
    private $danceGroup;

    /**
     * @var integer
     *
     * @ORM\Column(name="kind", type="smallint")
     */
    private $kind;


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
     * @return DanceType
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
     * Set danceGroup
     *
     * @param integer $danceGroup
     * @return DanceType
     */
    public function setDanceGroup($danceGroup)
    {
        $this->danceGroup = $danceGroup;
    
        return $this;
    }

    /**
     * Get danceGroup
     *
     * @return integer 
     */
    public function getDanceGroup()
    {
        return $this->danceGroup;
    }

    /**
     * Set kind
     *
     * @param integer $kind
     * @return DanceType
     */
    public function setKind($kind)
    {
        $this->kind = $kind;
    
        return $this;
    }

    /**
     * Get kind
     *
     * @return integer 
     */
    public function getKind()
    {
        return $this->kind;
    }

    /********
     * HELPER FUNCTIONS
     ********/
    /**
     * @param bool $keysOnly
     * @return array
     */
    public static function getAvaliableKinds($keysOnly = false)
    {
        $kinds = array(
            DanceType::DANCE_TYPE_KIND_SOLO => 'label.dance_type.kind.solo',
            DanceType::DANCE_TYPE_KIND_COUPLE => 'label.dance_type.kind.couple',
        );

        if ($keysOnly) {
             return array_keys($kinds);
        } else {
            return $kinds;
        }
    }

    /**
     * Get kind label
     */
    public function getKindLabel()
    {
        $kinds = self::getAvaliableKinds();

        return $kinds[$this->getKind()];
    }

    /**
     * @param ExecutionContext $context
     */
    public function validateKind(ExecutionContext $context)
    {
        if (!in_array($this->getKind(), self::getAvaliableKinds(true))) {
            $context->addViolationAt('kind', 'error.dance_type.kind', array(), null);
        }
    }

    /**
     * Get string representation of object
     */
    public function __toString()
    {
        return $this->getName();
    }
}
