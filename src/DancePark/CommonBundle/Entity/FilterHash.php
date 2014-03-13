<?php

namespace DancePark\CommonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FilterHash
 *
 * @ORM\Table(name="filter_hash")
 * @ORM\Entity
 */
class FilterHash
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
     * @ORM\Column(name="hashcode", type="string", length=512)
     */
    private $hash;

    /**
     * @var string
     *
     * @ORM\Column(name="filters", type="text")
     */
    private $filters;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createAt;


    public function __construct()
    {
        $this->createAt = new \DateTime();
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
     * Set hash
     *
     * @param string $hash
     * @return FilterHash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    
        return $this;
    }

    /**
     * Get hash
     *
     * @return string 
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set filters
     *
     * @param string $filters
     * @return FilterHash
     */
    public function setFilters($filters)
    {
        $this->filters = $filters;
    
        return $this;
    }

    /**
     * Get filters
     *
     * @return string 
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param \DateTime $createAt
     */
    public function setCreateAt($createAt)
    {
        $this->createAt = $createAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreateAt()
    {
        return $this->createAt;
    }
}
