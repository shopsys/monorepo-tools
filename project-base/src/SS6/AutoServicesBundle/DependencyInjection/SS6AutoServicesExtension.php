<?php

namespace SS6\AutoServicesBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class SS6AutoServicesExtension extends Extension {

	/**
	 * {@inheritDoc}
	 */
	public function load(array $configs, ContainerBuilder $containerBuilder) {
		$configuration = new Configuration();
		$config = $this->processConfiguration($configuration, $configs);

		$loader = new Loader\YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../Resources/config'));
		$loader->load('services.yml');

		$autoServicesCollectorDefinition = $containerBuilder->getDefinition('ss6.auto_services.auto_services_collector');
		$autoServicesCollector = $containerBuilder->resolveServices($autoServicesCollectorDefinition);
		/* @var $autoServicesCollector \SS6\AutoServicesBundle\Compiler\AutoServicesCollector */
		$containerInvalidatorFilepath = $autoServicesCollector->getContainerInvalidatorFilepath();
		if (file_exists($containerInvalidatorFilepath)) {
			$containerBuilder->addResource(new FileResource($containerInvalidatorFilepath));
		}
	}

}
