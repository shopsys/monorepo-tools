<?php

namespace SS6\AutoServicesBundle;

use SS6\AutoServicesBundle\Compiler\AutowiringCompilerPass;
use SS6\AutoServicesBundle\Compiler\ClassConstructorFiller;
use SS6\AutoServicesBundle\Compiler\ClassResolver;
use SS6\AutoServicesBundle\Compiler\ParameterProcessor;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SS6AutoServicesBundle extends Bundle {

	public function build(ContainerBuilder $containerBuilder) {

		$containerBuilder->addCompilerPass(
			new AutowiringCompilerPass(
				new ClassConstructorFiller(
					new ParameterProcessor(
						new ClassResolver(),
						$containerBuilder
					)
				)
			), PassConfig::TYPE_BEFORE_REMOVING
		);
	}

}
