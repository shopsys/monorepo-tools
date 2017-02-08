<?php

namespace Shopsys\AutoServicesBundle\Compiler;

use Shopsys\AutoServicesBundle\Compiler\ServiceHelper;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ControllerCompilerPass implements CompilerPassInterface {

	/**
	 * @var \Shopsys\AutoServicesBundle\Compiler\ServiceHelper
	 */
	private $serviceHelper;

	/**
	 * @param \Shopsys\AutoServicesBundle\Compiler\ServiceHelper $serviceHelper
	 */
	public function __construct(ServiceHelper $serviceHelper) {
		$this->serviceHelper = $serviceHelper;
	}

	/**
	 * @param \Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder
	 */
	public function process(ContainerBuilder $containerBuilder) {
		$srcRealpath = realpath($containerBuilder->getParameter('kernel.root_dir') . '/../src');

		foreach ($this->getAllControllerFiles($srcRealpath) as $controllerFile) {
			$controllerClassName = $this->getControllerClassName($srcRealpath, $controllerFile);
			if ($this->serviceHelper->canBeService($controllerClassName)) {
				$definition = new Definition($controllerClassName);
				$serviceId = $this->serviceHelper->convertClassNameToServiceId($controllerClassName);
				if ($this->isContainerAware($controllerClassName)) {
					$containerReference = new Reference('ss6.auto_services.auto_container');
					$definition->addMethodCall('setContainer', [$containerReference]);
				}
				$containerBuilder->setDefinition($serviceId, $definition);
			}
		}
	}

	/**
	 * @param string $srcPath
	 * @return \Symfony\Component\Finder\SplFileInfo[]
	 */
	private function getAllControllerFiles($srcPath) {
		$finder = new Finder();

		return $finder
			->in($srcPath . '/*/*/Controller/')
			->name('*Controller.php');
	}

	/**
	 * @param string $srcRealPath
	 * @param \Symfony\Component\Finder\SplFileInfo $controllerFile
	 * @return string
	 */
	private function getControllerClassName($srcRealPath, SplFileInfo $controllerFile) {
		$controllerRelativePathFromSrc = substr(realpath($controllerFile->getPath()), strlen($srcRealPath) + 1);
		$controllerNamespace = str_replace('/', '\\', $controllerRelativePathFromSrc);

		return $controllerNamespace . '\\' . $controllerFile->getBasename('.php');
	}

	/**
	 * @param string $className
	 * @return bool
	 */
	public function isContainerAware($className) {
		$interfaces = class_implements($className);

		return array_key_exists(ContainerAwareInterface::class, $interfaces);
	}

}
