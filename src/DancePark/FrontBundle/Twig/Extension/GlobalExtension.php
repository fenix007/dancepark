<?php
namespace DancePark\FrontBundle\Twig\Extension;

use DancePark\FrontBundle\Component\ServicesBag\ServicesBag;

class GlobalExtension extends \Twig_Extension
{
    protected $servicesBag;

    /**
     * Set object defaults
     *
     * @param ServicesBag $servicesBag
     */
    public function __construct(ServicesBag $servicesBag)
    {
        $this->servicesBag = $servicesBag;
    }

    public function getName()
    {
        return 'front_global_extension';
    }

    public function getGlobals()
    {
        $array = array();

        foreach ($this->servicesBag as $name => $service) {
            $array[$name] = $service;
        }

        return $array;
    }
}