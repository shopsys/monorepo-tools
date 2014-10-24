<?php

namespace SS6\ShopBundle\Model\Domain\Config;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class DomainsConfigDefinition implements ConfigurationInterface {

	const CONFIG_DOMAINS = 'domains';
	const CONFIG_ID = 'id';
	const CONFIG_DOMAIN = 'domain';
	const CONFIG_LOCALE = 'locale';
	const CONFIG_TEMPLATES_DIRECTORY = 'templates_directory';

	/**
	 * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder
	 */
	public function getConfigTreeBuilder() {
		$treeBuilder = new TreeBuilder();
		$rootNode = $treeBuilder->root('domains');

		$rootNode
			->children()
				->arrayNode(self::CONFIG_DOMAINS)
					->prototype('array')
						->children()
							->scalarNode(self::CONFIG_ID)->isRequired()->cannotBeEmpty()->end()
							->scalarNode(self::CONFIG_DOMAIN)->isRequired()->cannotBeEmpty()->end()
							->scalarNode(self::CONFIG_LOCALE)->isRequired()->cannotBeEmpty()->end()
							->scalarNode(self::CONFIG_TEMPLATES_DIRECTORY)->isRequired()->cannotBeEmpty()->end()
						->end()
					->end()
				->end()
			->end();

		return $treeBuilder;
	}

}
