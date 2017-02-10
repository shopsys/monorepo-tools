<?php

namespace Shopsys\ShopBundle\Component\Domain\Config;

use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class DomainsConfigDefinition implements ConfigurationInterface
{
    const CONFIG_DOMAINS = 'domains';
    const CONFIG_ID = 'id';
    const CONFIG_NAME = 'name';
    const CONFIG_LOCALE = 'locale';
    const CONFIG_STYLES_DIRECTORY = 'styles_directory';

    /**
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder
     */
    public function getConfigTreeBuilder() {
        $treeBuilder = new TreeBuilder();
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
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
