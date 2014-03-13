<?php
namespace DancePark\FrontBundle\Component\ServicesBag;

/**
 *Bag for services collections
 *
 * Class ServicesBag
 * @package DancePark\FrontBundle\Component\ServicesBag
 */
class ServicesBag implements BagInterface
{
    protected $_storage;

    /**
     * Construct object
     */
    public function __construct()
    {
         $this->_storage = array();
    }

    /**
     * {@inheritDoc}
     */
    public function has($key)
    {
         return isset($this->_storage[$key]);
    }

    /**
     * {@inheritDoc}
     */
    public function get($key, $default = null)
    {
        if ($this->has($key)) {
             return $this->_storage[$key];
        } else if($default) {
             return $default;
        } else {
            throw new \InvalidArgumentException("Can't find service with name: " . $key . " on service bag.");
        }
    }

    /**
     * {@inheritDoc}
     */
    public function add($key, $value)
    {
         $this->_storage[$key] = $value;
    }
}