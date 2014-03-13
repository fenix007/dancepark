<?php

namespace DancePark\FrontBundle;

use DancePark\FrontBundle\DependencyInjection\Compiler\ServicesBagCompiler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class FrontBundle extends Bundle
{
    /**
     * {@inheritDoc}
     * @param ContainerBuilder $containerBuilder
     */
    public function build(ContainerBuilder $containerBuilder)
    {
         $containerBuilder->addCompilerPass(new ServicesBagCompiler());
    }
}
