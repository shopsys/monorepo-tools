<?php

namespace Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterFriendlyUrlDataProviderCompilerPass implements CompilerPassInterface
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $friendlyUrlDataProviderConfigDefinition = $container->findDefinition(FriendlyUrlDataProviderRegistry::class);

        $taggedServiceIds = $container->findTaggedServiceIds('shopsys.friendly_url_provider');
        foreach ($taggedServiceIds as $serviceId => $tags) {
            $friendlyUrlDataProviderConfigDefinition->addMethodCall(
                'registerFriendlyUrlDataProvider',
                [
                    new Reference($serviceId),
                ]
            );
        }
    }
}
