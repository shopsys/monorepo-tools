<?php

namespace SS6\ShopBundle\Model\AdminMenu;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class MenuConfiguration implements ConfigurationInterface {

	public function getConfigTreeBuilder() {
		$treeBuilder = new TreeBuilder();
		$rootNode = $treeBuilder->root('menu');

		$this->buildItemsNode($rootNode->prototype('array'))->end();

		return $treeBuilder;
	}

	private function buildItemsNode(NodeDefinition $node) {
		return $node
			->addDefaultsIfNotSet()
			->children()
				->scalarNode('label')
				->end()
				->scalarNode('route')
					->defaultNull()
				->end()
				->arrayNode('route_parameters')
					->defaultValue(array())
					->prototype('array')
					->end()
				->end()
				->scalarNode('type')
					->defaultNull()
				->end()
				->variableNode('items')
					->defaultValue(array())
					->validate()->always(function(array $items) {
						foreach ($items as $i => $item) {
							/* @var $item \Symfony\Component\Config\Definition\NodeInterface */
							$itemsNode = $this->getItemsNode($i);
							$items[$i] = $itemsNode->normalize($items[$i]);
							$items[$i] = $itemsNode->finalize($items[$i]);
						}
						return $items;
					})->end()
				->end()
			->end();
	}

	/**
	 * @param string $name
	 * @return \Symfony\Component\Config\Definition\NodeInterface
	 */
	private function getItemsNode($name) {
		$treeBuilder = new TreeBuilder();
		$definition = $treeBuilder->root($name);
		
		$this->buildItemsNode($definition);

		return $definition->getNode(true);
	}

}
