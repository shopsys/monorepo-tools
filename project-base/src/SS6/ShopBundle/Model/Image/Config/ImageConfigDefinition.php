<?php

namespace SS6\ShopBundle\Model\Image\Config;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ImageConfigDefinition implements ConfigurationInterface {

	public function getConfigTreeBuilder() {
		$treeBuilder = new TreeBuilder();
		$rootNode = $treeBuilder->root('images');

		$this->buildItemsNode($rootNode->prototype('array'))->end();

		return $treeBuilder;
	}

	private function buildItemsNode(ArrayNodeDefinition $node) {
		return $node
			->addDefaultsIfNotSet()
			->children()
				->scalarNode('name')
				->end()
				->scalarNode('class')
				->end()
				->arrayNode('sizes')
					->defaultValue(array())
					->prototype('array')
					->children()
						->scalarNode('name')
						->end()
						->scalarNode('width')
						->end()
						->scalarNode('height')
						->end()
						->scalarNode('crop')
						->end()
					->end()
				->end()
				->end()
				->arrayNode('types')
					->defaultValue(array())
					->prototype('array')
					->children()
						->scalarNode('name')
						->end()
						->arrayNode('sizes')
							->defaultValue(array())
							->prototype('array')
							->children()
								->scalarNode('name')
								->end()
								->scalarNode('width')
								->end()
								->scalarNode('height')
								->end()
								->scalarNode('crop')
								->end()
							->end()
						->end()
					->end()
				->end()
			->end();
	}
}
