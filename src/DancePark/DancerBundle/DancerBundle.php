<?php

namespace DancePark\DancerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class DancerBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
