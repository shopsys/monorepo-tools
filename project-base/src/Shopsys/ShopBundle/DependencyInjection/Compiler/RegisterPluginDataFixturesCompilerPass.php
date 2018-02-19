<?php

namespace Shopsys\ShopBundle\DependencyInjection\Compiler;

use Shopsys\ShopBundle\Component\Plugin\PluginDataFixtureRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class RegisterPluginDataFixturesCompilerPass implements CompilerPassInterface
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $pluginDataFixtureRegistryDefinition = $container->findDefinition(
            PluginDataFixtureRegistry::class
        );
        $taggedServiceIds = $container->findTaggedServiceIds('shopsys.data_fixture');
        foreach (array_keys($taggedServiceIds) as $serviceId) {
            $this->registerDataFixture($pluginDataFixtureRegistryDefinition, $serviceId);
        }
    }

    /**
     * @param \Symfony\Component\DependencyInjection\Definition $pluginDataFixtureRegistryDefinition
     * @param string $serviceId
     */
    private function registerDataFixture(Definition $pluginDataFixtureRegistryDefinition, $serviceId)
    {
        $pluginDataFixtureRegistryDefinition->addMethodCall('registerDataFixture', [new Reference($serviceId)]);
    }
}
