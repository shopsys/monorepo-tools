<?php

namespace Shopsys\ShopBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterCronModulesCompilerPass implements CompilerPassInterface
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $cronConfigDefinition = $container->findDefinition('shopsys.shop.component.cron.config.cron_config');

        $taggedServiceIds = $container->findTaggedServiceIds('shopsys.cron');
        foreach ($taggedServiceIds as $serviceId => $tags) {
            foreach ($tags as $tag) {
                $cronConfigDefinition->addMethodCall(
                    'registerCronModule',
                    [
                        new Reference($serviceId),
                        $serviceId,
                        $tag['hours'],
                        $tag['minutes'],
                    ]
                );
            }
        }
    }
}
