<?php

namespace Shopsys\ShopBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * See {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        /** @var \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->root('shopsys_shop');

        $rootNode
            ->children()
                ->arrayNode('router')
                    ->children()
                        ->scalarNode('locale_router_filepath_mask')
                        ->end()
                        ->scalarNode('friendly_url_router_filepath')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
