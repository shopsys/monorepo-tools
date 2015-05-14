<?php

namespace SS6\ShopBundle\Component\Cron\Config;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class CronConfigDefinition implements ConfigurationInterface {

	const CONFIG_SERVICE = 'service';
	const CONFIG_TIME = 'time';
	const CONFIG_TIME_MINUTES = 'minutes';
	const CONFIG_TIME_HOURS = 'hours';

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
						->scalarNode(self::CONFIG_TIME_MINUTES)->cannotBeEmpty()->defaultValue('*')->end()
						->scalarNode(self::CONFIG_TIME_HOURS)->cannotBeEmpty()->defaultValue('*')->end()
					->end()
				->end()
			->end();
	}

}
