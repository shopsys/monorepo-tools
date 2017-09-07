<?php

namespace Shopsys\ShopBundle\DependencyInjection\Compiler;

use Shopsys\ShopBundle\Model\Feed\FeedConfigRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class RegisterProductFeedConfigsCompilerPass implements CompilerPassInterface
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $feedConfigRegistryDefinition = $container->findDefinition('shopsys.shop.feed.feed_config_registry');

        $taggedServiceIds = $container->findTaggedServiceIds('shopsys.product_feed');
        foreach ($taggedServiceIds as $serviceId => $tags) {
            foreach ($tags as $tag) {
                $this->registerFeedConfig($feedConfigRegistryDefinition, $serviceId, $tag['type'] ?? null);
            }
        }
    }

    /**
     * @param \Symfony\Component\DependencyInjection\Definition $feedConfigRegistryDefinition
     * @param string $serviceId
     * @param string|null $type
     */
    private function registerFeedConfig(Definition $feedConfigRegistryDefinition, $serviceId, $type)
    {
        $arguments = [new Reference($serviceId)];
        if ($type !== null) {
            FeedConfigRegistry::assertTypeIsKnown($type);
            $arguments[] = $type;
        }

        $feedConfigRegistryDefinition->addMethodCall('registerFeedConfig', $arguments);
    }
}
