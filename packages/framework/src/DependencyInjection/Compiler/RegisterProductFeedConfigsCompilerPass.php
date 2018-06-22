<?php

namespace Shopsys\FrameworkBundle\DependencyInjection\Compiler;

use Shopsys\FrameworkBundle\Model\Feed\FeedRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterProductFeedConfigsCompilerPass implements CompilerPassInterface
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container): void
    {
        $feedRegistryDefinition = $container->findDefinition(FeedRegistry::class);

        $taggedServiceIds = $container->findTaggedServiceIds('shopsys.product_feed');
        foreach ($taggedServiceIds as $serviceId => $tags) {
            foreach ($tags as $tag) {
                $type = $tag['type'] ?? null;
                $feedRegistryDefinition->addMethodCall('registerFeed', [new Reference($serviceId), $type]);
            }
        }
    }
}
