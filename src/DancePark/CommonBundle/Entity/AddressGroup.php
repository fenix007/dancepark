<?php

namespace DancePark\CommonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * AddressGroup
 *
 * @Gedmo\Tree(type="nested")
 * @ORM\Table(name="address_group")
 * @ORM\Entity(repositoryClass="DancePark\CommonBundle\Entity\AddressGroupRepository")
 */
class AddressGroup
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
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
     * @ORM\Column(name="prefix", type="string", length=255, nullable=true)
     * @Assert\Length(max=255, maxMessage="error.length255")
     */
    private $prefix;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="DancePark\CommonBundle\Entity\AddressLevel")
     * @ORM\JoinColumn(name="address_level_id")
     */
    private $addressLevel;

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     */
    private $lft;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    private $lvl;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     */
    private $rgt;

    /**
     * @Gedmo\TreeRoot
     * @ORM\Column(name="root", type="integer", nullable=true)
     */
    private $root;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="DancePark\CommonBundle\Entity\AddressGroup", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="DancePark\CommonBundle\Entity\AddressGroup", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;

    /**
     * @var $region
     *
     * @ORM\ManyToOne(targetEntity="DancePark\CommonBundle\Entity\AddressRegion")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="id")
     */
    private $region;


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
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }


    /**
     * Set name
     *
     * @param string $name
     * @return AddressGroup
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
     * Set prefix
     *
     * @param string $prefix
     * @return AddressGroup
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    
        return $this;
    }

    /**
     * Get prefix
     *
     * @return string 
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Set addressLevel
     *
     * @param integer $addressLevel
     * @return AddressGroup
     */
    public function setAddressLevel($addressLevel)
    {
        $this->addressLevel = $addressLevel;
    
        return $this;
    }

    /**
     * Get addressLevel
     *
     * @return integer 
     */
    public function getAddressLevel()
    {
        return $this->addressLevel;
    }

    /**
     * Set leaf parent
     *
     * @param null $parent
     * @return AddressGroup
     */
    public function setParent($parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get leaf parent
     *
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set lft
     *
     * @param integer $lft
     * @return AddressGroup
     */
    public function setLft($lft)
    {
        $this->lft = $lft;
    
        return $this;
    }

    /**
     * Get lft
     *
     * @return integer 
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * Set lvl
     *
     * @param integer $lvl
     * @return AddressGroup
     */
    public function setLvl($lvl)
    {
        $this->lvl = $lvl;
    
        return $this;
    }

    /**
     * Get lvl
     *
     * @return integer 
     */
    public function getLvl()
    {
        return $this->lvl;
    }

    /**
     * Set rgt
     *
     * @param integer $rgt
     * @return AddressGroup
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;
    
        return $this;
    }

    /**
     * Get rgt
     *
     * @return integer 
     */
    public function getRgt()
    {
        return $this->rgt;
    }

    /**
     * Set root
     *
     * @param integer $root
     * @return AddressGroup
     */
    public function setRoot($root)
    {
        $this->root = $root;
    
        return $this;
    }

    /**
     * Get root
     *
     * @return integer 
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Add children
     *
     * @param \DancePark\CommonBundle\Entity\AddressGroup $children
     * @return AddressGroup
     */
    public function addChildren(\DancePark\CommonBundle\Entity\AddressGroup $children)
    {
        $this->children[] = $children;
    
        return $this;
    }

    /**
     * Remove children
     *
     * @param \DancePark\CommonBundle\Entity\AddressGroup $children
     */
    public function removeChildren(\DancePark\CommonBundle\Entity\AddressGroup $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Get string object representations
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Get full address
     */
    public function getFullAddress()
    {
        if (!is_null($this->parent)) {
            return (string)$this->getParent() . ' ' . $this->getPrefix() . ' ' .  $this->getName();
        } else if ($this->prefix) {;
            return $this->getPrefix() . ' ' .  $this->getName();
        } else {
            return $this->getName();
        }
    }

    /**
     * Set region
     *
     * @param \DancePark\CommonBundle\Entity\AddressRegion $region
     * @return AddressGroup
     */
    public function setRegion(\DancePark\CommonBundle\Entity\AddressRegion $region = null)
    {
        $this->region = $region;
    
        return $this;
    }

    /**
     * Get region
     *
     * @return \DancePark\CommonBundle\Entity\AddressRegion 
     */
    public function getRegion()
    {
        return $this->region;
    }
}