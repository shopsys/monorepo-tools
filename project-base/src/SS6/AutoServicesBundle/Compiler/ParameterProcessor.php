<?php

namespace SS6\AutoServicesBundle\Compiler;

use ReflectionClass;
use ReflectionParameter;
use SS6\AutoServicesBundle\Compiler\ClassConstructorFiller;
use SS6\AutoServicesBundle\Compiler\ContainerClassList;
use SS6\AutoServicesBundle\Compiler\ServiceHelper;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ParameterProcessor {

	/**
	 * @var \SS6\AutoServicesBundle\Compiler\ServiceHelper
	 */
	private $classResolver;

	/**
	 * @var \Symfony\Component\DependencyInjection\ContainerBuilder
	 */
	private $containerBuilder;

	/**
	 * @var \SS6\AutoServicesBundle\Compiler\ClassConstructorFiller|null
	 */
	private $classConstructorFilter;

	/**
	 * @var bool[string]
	 */
	private $loading;

	/**
	 * @param \SS6\AutoServicesBundle\Compiler\ServiceHelper $classResolver
	 * @param \Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder
	 */
	public function __construct(
		ServiceHelper $classResolver,
		ContainerBuilder $containerBuilder
	) {
		$this->classResolver = $classResolver;
		$this->containerBuilder = $containerBuilder;
		$this->loading = [];
	}

	/**
	 * @param \SS6\AutoServicesBundle\Compiler\ClassConstructorFiller $classConstructorFilter
	 */
	public function injectClassConstructorFilter(ClassConstructorFiller $classConstructorFilter) {
		$this->classConstructorFilter = $classConstructorFilter;
	}

	/**
	 * @param \ReflectionParameter $parameter
	 * @param string $serviceId
	 * @param \SS6\AutoServicesBundle\Compiler\ContainerClassList $containerClassList
	 * @return mixed
	 */
	public function getParameterValue(ReflectionParameter $parameter, $serviceId, ContainerClassList $containerClassList) {
		$parameterClass = $parameter->getClass();

		if ($parameterClass) {
			$value = $this->processParameterClass($parameterClass, $parameter, $containerClassList);
		} else {
			if ($parameter->isDefaultValueAvailable()) {
				$value = $parameter->getDefaultValue();
			} else {
				$message = 'Class ' . $parameter->getDeclaringClass()->getName() . ' (service: ' . $serviceId
					. '), parameter $' . $parameter->getName();

				throw new \SS6\AutoServicesBundle\Compiler\Exception\CannotResolveParameterException($message);
			}
		}

		return $value;
	}

	/**
	 * @param \ReflectionClass $parameterClass
	 * @param \ReflectionParameter $parameter
	 * @param \SS6\AutoServicesBundle\Compiler\ContainerClassList $containerClassList
	 * @return \Symfony\Component\DependencyInjection\Reference
	 */
	private function processParameterClass(
		ReflectionClass $parameterClass,
		ReflectionParameter $parameter,
		ContainerClassList $containerClassList
	) {
		$class = $parameterClass->getName();

		try {
			return new Reference($containerClassList->getServiceIdByClass($class));
		} catch (\SS6\AutoServicesBundle\Compiler\Exception\ServiceClassNotFoundException $ex) {
			if ($parameter->isDefaultValueAvailable()) {
				return $parameter->getDefaultValue();
			} else {
				return new Reference($this->registerNewService($class, $containerClassList));
			}
		}
	}

	/**
	 * @param string $class
	 * @param \SS6\AutoServicesBundle\Compiler\ContainerClassList $containerClassList
	 * @return string
	 */
	private function registerNewService($class, ContainerClassList $containerClassList) {
		if (isset($this->loading[$class])) {
			throw new \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException(
				$class,
				array_keys($this->loading)
			);
		}
		if (!$this->classResolver->canBeService($class)) {
			throw new \SS6\AutoServicesBundle\Compiler\Exception\ServiceClassNotFoundException($class);
		}
		$serviceId = $this->classResolver->convertClassNameToServiceId($class);
		$this->loading[$class] = true;

		$definition = new Definition($class);
		$reflection = new ReflectionClass($class);
		$definition->setFile($reflection->getFileName());
		$constructor = $reflection->getConstructor();
		$this->classConstructorFilter->autowireParams($constructor, $serviceId, $definition, $containerClassList);
		$this->containerBuilder->setDefinition($serviceId, $definition);
		$this->watchServiceClassForChanges($reflection);
		$containerClassList->addClass($serviceId, $class);
		unset($this->loading[$class]);

		return $serviceId;
	}

	/**
	 * @param \ReflectionClass $reflectionClass
	 */
	private function watchServiceClassForChanges(ReflectionClass $reflectionClass) {
		do {
			$this->containerBuilder->addResource(new FileResource($reflectionClass->getFileName()));
		} while ($reflectionClass = $reflectionClass->getParentClass());
	}

}
