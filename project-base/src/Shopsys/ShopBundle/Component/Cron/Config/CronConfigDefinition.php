<?php

namespace Shopsys\ShopBundle\Component\Cron\Config;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class CronConfigDefinition implements ConfigurationInterface {

    const CONFIG_SERVICE = 'service';
    const CONFIG_TIME = 'time';
    const CONFIG_TIME_HOURS = 'hours';
    const CONFIG_TIME_MINUTES = 'minutes';

    /**
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder
     */
    public function getConfigTreeBuilder() {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('cron');

        $this->buildItemsNode($rootNode->prototype('array'))->end();

        return $treeBuilder;
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    private function buildItemsNode(ArrayNodeDefinition $node) {
        return $node
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode(self::CONFIG_SERVICE)->isRequired()->cannotBeEmpty()->end()
                ->arrayNode(self::CONFIG_TIME)
                    ->children()
                        ->scalarNode(self::CONFIG_TIME_HOURS)->defaultValue('*')->end()
                        ->scalarNode(self::CONFIG_TIME_MINUTES)->defaultValue('*')->end()
                    ->end()
                ->end()
            ->end();
    }

}
