<?php
namespace DancePark\FrontBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ServicesBagCompiler implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        $servicesBags = $container->findTaggedServiceIds("services_bag");

        foreach ($servicesBags as $serviceId => $attrSet) {

            foreach($attrSet as $id => $data) {
                $servicesBag = $container->getDefinition($serviceId);

                $taggedServices = $container->findTaggedServiceIds($data['alias']);

                foreach ($taggedServices as $serviceId => $attributesSet) {
                    $servicesBag->addMethodCall('add', array(
                        $serviceId,
                        new Reference($serviceId)
                    ));
                }
            }
        }
    }
}