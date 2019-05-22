<?php

namespace Shopsys\FrameworkBundle\Component\Domain\Config;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class DomainsConfigDefinition implements ConfigurationInterface
{
    public const CONFIG_DOMAINS = 'domains';
    public const CONFIG_ID = 'id';
    public const CONFIG_NAME = 'name';
    public const CONFIG_LOCALE = 'locale';
    public const CONFIG_STYLES_DIRECTORY = 'styles_directory';
    public const CONFIG_DESIGN_ID = 'design_id';

    /**
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        /** @var \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->root('domains');

        $rootNode
            ->children()
                ->arrayNode(self::CONFIG_DOMAINS)
                    ->useAttributeAsKey(self::CONFIG_ID, false)
                    ->prototype('array')
                        ->children()
                            ->scalarNode(self::CONFIG_ID)->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode(self::CONFIG_NAME)->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode(self::CONFIG_LOCALE)->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode(self::CONFIG_STYLES_DIRECTORY)->defaultValue(DomainConfig::STYLES_DIRECTORY_DEFAULT)->end()
                            ->scalarNode(self::CONFIG_DESIGN_ID)->defaultNull()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
