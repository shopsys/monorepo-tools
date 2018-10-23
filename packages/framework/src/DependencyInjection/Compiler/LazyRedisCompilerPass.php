<?php

namespace Shopsys\FrameworkBundle\DependencyInjection\Compiler;

use Doctrine\Common\Cache\RedisCache;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Set Redis-related services as lazy because it might not even be connected (e.g during Docker image build)
 */
class LazyRedisCompilerPass implements CompilerPassInterface
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container): void
    {
        $container->getDefinition('session')
            ->setLazy(true);

        foreach ($container->getDefinitions() as $definition) {
            if ($definition->getClass() === RedisCache::class) {
                $definition->setLazy(true);
            }
        }
    }
}
