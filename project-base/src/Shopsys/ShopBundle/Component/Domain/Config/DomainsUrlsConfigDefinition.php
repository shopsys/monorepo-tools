<?php

namespace Shopsys\ShopBundle\Component\Domain\Config;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class DomainsUrlsConfigDefinition implements ConfigurationInterface
{

    const CONFIG_DOMAINS_URLS = 'domains_urls';
    const CONFIG_ID = 'id';
    const CONFIG_URL = 'url';

    /**
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder
     */
    public function getConfigTreeBuilder() {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root(self::CONFIG_DOMAINS_URLS);

        $rootNode
            ->children()
                ->arrayNode(self::CONFIG_DOMAINS_URLS)
                ->useAttributeAsKey(self::CONFIG_ID, false)
                    ->prototype('array')
                        ->children()
                            ->scalarNode(self::CONFIG_ID)->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode(self::CONFIG_URL)->isRequired()->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
