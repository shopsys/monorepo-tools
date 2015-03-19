<?php

namespace SS6\AutoServicesBundle;

use SS6\AutoServicesBundle\Compiler\AutowiringCompilerPass;
use SS6\AutoServicesBundle\Compiler\ClassConstructorFiller;
use SS6\AutoServicesBundle\Compiler\ControllerCompilerPass;
use SS6\AutoServicesBundle\Compiler\ParameterProcessor;
use SS6\AutoServicesBundle\Compiler\ServiceHelper;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SS6AutoServicesBundle extends Bundle {

	public function build(ContainerBuilder $containerBuilder) {
		$parameterProcessor = new ParameterProcessor(new ServiceHelper(),	$containerBuilder);
		$classConstructorFiller = new ClassConstructorFiller($parameterProcessor);

		/**
		 * ControllerCompilerPass must be added before AutowiringCompilerPass
		 * because of controllers autowired dependencies
		 */
		$containerBuilder->addCompilerPass(
			new ControllerCompilerPass(new ServiceHelper()),
			PassConfig::TYPE_BEFORE_REMOVING
		);

		$containerBuilder->addCompilerPass(
			new AutowiringCompilerPass($classConstructorFiller),
			PassConfig::TYPE_BEFORE_REMOVING
		);
	}

}
