<?php

namespace SS6\AutoServicesBundle\Compiler;

use SS6\AutoServicesBundle\Compiler\ServiceHelper;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Finder\Finder;

class ControllerCompilerPass implements CompilerPassInterface {

	const CONTROLLERS_FOLDER_PATH = 'SS6/ShopBundle/Controller/*/';

	private $serviceHelper;

	/**
	 * @param \SS6\AutoServicesBundle\Compiler\ServiceHelper $serviceHelper
	 */
	public function __construct(ServiceHelper $serviceHelper) {
		$this->serviceHelper = $serviceHelper;
	}

	/**
	 * @param \Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder
	 */
	public function process(ContainerBuilder $containerBuilder) {
		$this->processAutoServicesCollectorData($containerBuilder);
	}

	/**
	 * @param \Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder
	 */
	private function processAutoServicesCollectorData(ContainerBuilder $containerBuilder) {
		$srcPath = $containerBuilder->getParameter('kernel.root_dir') . '/../src/';
		$srcPathLength = strlen($srcPath);
		foreach ($this->getAllControllers($srcPath) as $controller) {
			$controllerClass = substr($controller->getPath(), $srcPathLength) . '\\' . substr($controller->getFilename(), 0, -4);
			$controllerClassWithBackSlashes = strtr($controllerClass, '/', '\\');
			if ($this->serviceHelper->canBeService($controllerClassWithBackSlashes)) {
				$definition = new Definition($controllerClassWithBackSlashes);
				$controllerId = $this->serviceHelper->convertClassNameToServiceId($controllerClassWithBackSlashes);
				$containerBuilder->setDefinition($controllerId, $definition);
			}
		}
	}

	/**
	 * @param string $srcPath
	 * @return \SplFileInfo
	 */
	private function getAllControllers($srcPath) {
		$finder = new Finder();
		$controllers = $finder
			->in($srcPath . self::CONTROLLERS_FOLDER_PATH)
			->name('*Controller.php');

		return $controllers;
	}

}
