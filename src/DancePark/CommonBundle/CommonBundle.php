<?php

namespace DancePark\CommonBundle;

use DancePark\CommonBundle\DependencyInjection\Compiler\CheckDefaultsServicesCompiler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CommonBundle extends Bundle
{
    /**
     * @param ContainerBuilder $builder
     */
    public function build(ContainerBuilder $builder)
    {
        $builder->addCompilerPass(new CheckDefaultsServicesCompiler());
    }
}
