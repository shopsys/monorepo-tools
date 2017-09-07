<?php

namespace Shopsys\ShopBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class RegisterProductFeedConfigsCompilerPass implements CompilerPassInterface
{
    const TYPE_DEFAULT = 'default';
    const TYPE_DELIVERY = 'delivery';

    const REGISTER_METHOD_BY_TYPE = [
        self::TYPE_DEFAULT => 'registerFeedConfig',
        self::TYPE_DELIVERY => 'registerDeliveryFeedConfig',
    ];

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $feedConfigRegistryDefinition = $container->findDefinition('shopsys.shop.feed.feed_config_registry');

        $taggedServiceIds = $container->findTaggedServiceIds('shopsys.product_feed');
        foreach ($taggedServiceIds as $serviceId => $tags) {
            foreach ($tags as $tag) {
                $type = array_key_exists('type', $tag) ? $tag['type'] : self::TYPE_DEFAULT;

                $this->registerFeedConfig($feedConfigRegistryDefinition, $serviceId, $type);
            }
        }
    }

    /**
     * @param \Symfony\Component\DependencyInjection\Definition $feedConfigRegistryDefinition
     * @param string $serviceId
     * @param string $type
     */
    private function registerFeedConfig(Definition $feedConfigRegistryDefinition, $serviceId, $type)
    {
        if (!array_key_exists($type, self::REGISTER_METHOD_BY_TYPE)) {
            throw new \Shopsys\ShopBundle\Model\Feed\Exception\UnknownFeedConfigTypeException(
                $serviceId,
                $type,
                array_keys(self::REGISTER_METHOD_BY_TYPE)
            );
        }

        $registerMethod = self::REGISTER_METHOD_BY_TYPE[$type];
        $serviceReference = new Reference($serviceId);

        $feedConfigRegistryDefinition->addMethodCall($registerMethod, [$serviceReference]);
    }
}
