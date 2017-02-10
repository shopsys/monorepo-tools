<?php

namespace Shopsys\AutoServicesBundle;

use Shopsys\AutoServicesBundle\Compiler\AutowiringCompilerPass;
use Shopsys\AutoServicesBundle\Compiler\ClassConstructorFiller;
use Shopsys\AutoServicesBundle\Compiler\ControllerCompilerPass;
use Shopsys\AutoServicesBundle\Compiler\ParameterProcessor;
use Shopsys\AutoServicesBundle\Compiler\ServiceHelper;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ShopsysAutoServicesBundle extends Bundle {

	public function build(ContainerBuilder $containerBuilder) {
		$serviceHelper = new ServiceHelper();
		$parameterProcessor = new ParameterProcessor($serviceHelper,	$containerBuilder);
		$classConstructorFiller = new ClassConstructorFiller($parameterProcessor);

		/**
		 * ControllerCompilerPass must be added before AutowiringCompilerPass
		 * because of controllers autowired dependencies
		 */
		$containerBuilder->addCompilerPass(
			new ControllerCompilerPass($serviceHelper),
			PassConfig::TYPE_BEFORE_REMOVING
		);

		$containerBuilder->addCompilerPass(
			new AutowiringCompilerPass($classConstructorFiller),
			PassConfig::TYPE_BEFORE_REMOVING
		);
	}

}
