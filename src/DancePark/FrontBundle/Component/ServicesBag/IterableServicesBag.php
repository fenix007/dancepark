<?php
namespace DancePark\FrontBundle\Component\ServicesBag;

/**
 * Class IterableServicesBag
 * @package DancePark\FrontBundle\Component\ServicesBag
 */
class IterableServicesBag extends ServicesBag implements \Iterator {

    /**
     * {@inheritDoc}
     */
    public function current()
    {
        return current($this->_storage);
    }

    /**
     * {@inheritDoc}
     */
    public function next()
    {
        return next($this->_storage);
    }

    /**
     * {@inheritDoc}
     */
    public function key()
    {
        return key($this->_storage);
    }

    /**
     * {@inheritDoc}
     */
    public function valid()
    {
        return isset($this->_storage[$this->key()]);
    }

    /**
     * {@inheritDoc}
     */
    public function rewind()
    {
        return reset($this->_storage);
    }
}