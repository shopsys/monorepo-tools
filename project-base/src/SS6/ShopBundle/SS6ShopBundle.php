<?php

namespace SS6\ShopBundle;

use SS6\ShopBundle\DependencyInjection\Compiler\ValidatorBuilderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SS6ShopBundle extends Bundle {

	/**
	 * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
	 */
	public function build(ContainerBuilder $container) {
		parent::build($container);

		$container->addCompilerPass(new ValidatorBuilderCompilerPass());
	}

}
