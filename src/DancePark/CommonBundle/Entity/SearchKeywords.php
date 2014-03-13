<?php

namespace DancePark\CommonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContext;

/**
 * SearchKeywords
 *
 * @ORM\Table(name="search_keywords")
 * @ORM\Entity(repositoryClass="DancePark\CommonBundle\Entity\SearchKeywordsRepository")
 * @Assert\Callback(methods={"validateType"})
 */
class SearchKeywords
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
     *
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="smallint")
     */
    private $type;

    // Type constants
    const SEARCH_KEYWORD_TYPE_NAME = 1;
    const SEARCH_KEYWORD_TYPE_TYPE = 2;
    const SEARCH_KEYWORD_TYPE_DANCE_TYPE = 3;
    const SEARCH_KEYWORD_TYPE_INFO = 4;
    const SEARCH_KEYWORD_TYPE_PLACE = 5;


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
     * @return SearchKeywords
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
     * Set type
     *
     * @param integer $type
     * @return SearchKeywords
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get search keywords types
     *
     * @param bool $keysOnly
     * @return array
     */
    public static  function getKeywordsTypes($keysOnly = false)
    {
        $types = array(
            self::SEARCH_KEYWORD_TYPE_NAME => 'label.event_name',
            self::SEARCH_KEYWORD_TYPE_TYPE => 'label.event_type',
            self::SEARCH_KEYWORD_TYPE_DANCE_TYPE => 'label.dance_type',
            self::SEARCH_KEYWORD_TYPE_INFO => 'label.event_info',
            self::SEARCH_KEYWORD_TYPE_PLACE => 'label.event_place'
        );

        if ($keysOnly) {
            return array_keys($types);
        } else {
            return $types;
        }
    }

    /**
     * Get label for current entity object
     */
    public function getTypeName()
    {
        $types = $this->getKeywordsTypes();

        return $types[$this->type];
    }

    /**
     * Validate callback for type field
     *
     * @param ExecutionContext $context
     */
    public function validateType(ExecutionContext $context)
    {
         if (!empty($this->type) && !in_array($this->type, self::getKeywordsTypes(true))) {
            $context->addViolationAt('type', 'label.search_keywords_type');
         }
    }
}
