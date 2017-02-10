<?php

namespace Shopsys\ShopBundle\Component\Image\Config;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ImageConfigDefinition implements ConfigurationInterface
{
    const CONFIG_CLASS = 'class';
    const CONFIG_ENTITY_NAME = 'name';
    const CONFIG_MULTIPLE = 'multiple';
    const CONFIG_TYPES = 'types';
    const CONFIG_TYPE_NAME = 'name';
    const CONFIG_SIZES = 'sizes';
    const CONFIG_SIZE_NAME = 'name';
    const CONFIG_SIZE_WIDTH = 'width';
    const CONFIG_SIZE_HEIGHT = 'height';
    const CONFIG_SIZE_CROP = 'crop';

    /**
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('images');

        $this->buildItemsNode($rootNode->prototype('array'))->end();

        return $treeBuilder;
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    private function buildItemsNode(ArrayNodeDefinition $node)
    {
        return $node
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode(self::CONFIG_ENTITY_NAME)->isRequired()->cannotBeEmpty()->end()
                ->scalarNode(self::CONFIG_CLASS)->isRequired()->cannotBeEmpty()->end()
                ->scalarNode(self::CONFIG_MULTIPLE)->defaultFalse()->end()
                ->arrayNode(self::CONFIG_SIZES)
                    ->defaultValue([])
                    ->prototype('array')
                    ->children()
                        ->scalarNode(self::CONFIG_SIZE_NAME)->isRequired()->end()
                        ->scalarNode(self::CONFIG_SIZE_WIDTH)->end()
                        ->scalarNode(self::CONFIG_SIZE_HEIGHT)->end()
                        ->scalarNode(self::CONFIG_SIZE_CROP)->defaultFalse()->end()
                    ->end()
                ->end()
                ->end()
                ->arrayNode(self::CONFIG_TYPES)
                    ->defaultValue([])
                    ->prototype('array')
                    ->children()
                        ->scalarNode(self::CONFIG_TYPE_NAME)->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode(self::CONFIG_MULTIPLE)->defaultFalse()->end()
                        ->arrayNode(self::CONFIG_SIZES)
                            ->defaultValue([])
                            ->prototype('array')
                            ->children()
                                ->scalarNode(self::CONFIG_SIZE_NAME)->isRequired()->end()
                                ->scalarNode(self::CONFIG_SIZE_WIDTH)->end()
                                ->scalarNode(self::CONFIG_SIZE_HEIGHT)->end()
                                ->scalarNode(self::CONFIG_SIZE_CROP)->defaultFalse()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
