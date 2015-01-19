<?php

namespace SS6\ShopBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ValidatorBuilderCompilerPass implements CompilerPassInterface {

	/**
	 * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
	 */
	public function process(ContainerBuilder $container) {
		$validatorBuilderDefinition = $container->getDefinition('validator.builder');

		$validatorBuilderDefinition->addMethodCall('addLoader', array(
			new Reference('ss6.shop.component.validator.auto_validator_annotation_loader'),
		));
	}

}
