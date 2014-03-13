<?php
namespace DancePark\FrontBundle\Component\ServicesBag;

interface BagInterface {
    /**
     * Check if service with key $key is exists
     *
     * @param $key
     * @return mixed
     */
    public function has($key);

    /**
     * Get service with key $key, $default or throw exception
     *
     * @param $key
     * @param null $default
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public function get($key, $default = null);

    /**
     * Add new item to bag with kry $key
     *
     * @param $key
     * @param $value
     * @return mixed
     */
    public function add($key, $value);
}