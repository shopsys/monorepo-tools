<?php

namespace Shopsys\IntegrationTestingBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Collects all services that should be available for ServiceByTypeLocator.
 *
 * This pass first publishes all autowired services and then collects all public
 * services and their types so that this list can be used by ServiceByTypeLocator.
 *
 * @see \Shopsys\IntegrationTestingBundle\ServiceLocator\ServiceByTypeLocator
 */
class CollectClassNameByServiceIdMapCompilerPass implements CompilerPassInterface
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $definitions = $container->getDefinitions();

        $classNameByServiceId = [];
        foreach ($definitions as $serviceId => $definition) {
            /**
             * Make all autowired services public otherwise InlineServiceDefinitionsPass
             * will inline them and it will not be possible to get them from Container.
             * @see \Symfony\Component\DependencyInjection\Compiler\InlineServiceDefinitionsPass::isInlineableDefinition()
             */
            if (strpos($serviceId, 'autowired.') === 0) {
                $definition->setPublic(true);
            }

            if ($definition->isPublic()) {
                $classNameByServiceId[$serviceId] = $definition->getClass();
            }
        }

        $classNameByServiceIdMapFilename = $container
            ->getParameter('shopsys_integration_testing.class_name_by_service_id_map_filename');

        file_put_contents($classNameByServiceIdMapFilename, json_encode($classNameByServiceId));
    }
}
