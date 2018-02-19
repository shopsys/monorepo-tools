<?php

namespace Shopsys\ShopBundle\DependencyInjection\Compiler;

use Shopsys\ShopBundle\Component\Plugin\PluginCrudExtensionRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class RegisterPluginCrudExtensionsCompilerPass implements CompilerPassInterface
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $pluginCrudExtensionRegistryDefinition = $container->findDefinition(
            PluginCrudExtensionRegistry::class
        );

        $taggedServiceIds = $container->findTaggedServiceIds('shopsys.crud_extension');
        foreach ($taggedServiceIds as $serviceId => $tags) {
            foreach ($tags as $tag) {
                $this->registerPluginCrudExtension($pluginCrudExtensionRegistryDefinition, $serviceId, $tag['type']);
            }
        }
    }

    /**
     * @param \Symfony\Component\DependencyInjection\Definition $pluginCrudExtensionRegistryDefinition
     * @param string $serviceId
     * @param string $type
     */
    private function registerPluginCrudExtension(Definition $pluginCrudExtensionRegistryDefinition, $serviceId, $type)
    {
        PluginCrudExtensionRegistry::assertTypeIsKnown($type);

        $pluginCrudExtensionRegistryDefinition->addMethodCall(
            'registerCrudExtension',
            [new Reference($serviceId), $type, $serviceId]
        );
    }
}
