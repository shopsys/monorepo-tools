<?php

namespace SS6\AutoServicesBundle\Compiler;

use ReflectionClass;
use SS6\AutoServicesBundle\Compiler\AutoServicesCollector;
use SS6\AutoServicesBundle\Compiler\ClassResolver;
use SS6\AutoServicesBundle\Compiler\ContainerClassList;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AutoContainer implements ContainerInterface {

	/**
	 * @var \SS6\AutoServicesBundle\Compiler\ClassResolver
	 */
	private $classResolver;

	/**
	 * @var \Symfony\Component\DependencyInjection\ContainerInterface
	 */
	protected $container;

	/**
	 * @var \SS6\AutoServicesBundle\Compiler\ContainerClassList
	 */
	private $containerClassList;

	/**
	 * @var \SS6\AutoServicesBundle\Compiler\AutoServicesCollector
	 */
	private $autoServiceCollector;

	public function __construct(
		ContainerInterface $container,
		ClassResolver $classResolver,
		ContainerClassList $containerClassList,
		AutoServicesCollector $autoServiceCollector
	) {
		$this->classResolver = $classResolver;
		$this->container = $container;
		$this->containerClassList = $containerClassList;
		$this->autoServiceCollector = $autoServiceCollector;
	}

	public function get($serviceId, $invalidBehavior = self::EXCEPTION_ON_INVALID_REFERENCE) {
		if ($this->container->has($serviceId)) {
			return $this->container->get($serviceId, $invalidBehavior);
		}

		try {
			return $this->findServiceByClassName($serviceId);
		} catch (\Exception $e) {
			if (self::EXCEPTION_ON_INVALID_REFERENCE === $invalidBehavior) {
				throw new \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException($serviceId, null, $e);
			}
			return null;
		}
	}

	/**
	 * @param string $className
	 * @return object
	 */
	private function findServiceByClassName($className) {
		try {
			$classServiceId = $this->containerClassList->getServiceIdByClass($className);
			return $this->container->get($classServiceId);
		} catch (\SS6\AutoServicesBundle\Compiler\Exception\ServiceClassNotFoundException $e) {
			return $this->getServiceByClassName($className);
		}
	}

	/**
	 * @param string $className
	 * @return mixed
	 */
	private function getServiceByClassName($className) {
		$classServiceId = $this->classResolver->convertClassNameToServiceId($className);
		try {
			$service = $this->container->get($classServiceId, self::EXCEPTION_ON_INVALID_REFERENCE);
		} catch (\Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException $exception) {
			$service = $this->createServiceByClassName($className);
			$this->registerServiceToContainer($classServiceId, $service, $className);
		}

		return $service;
	}

	/**
	 * @param string $serviceId
	 * @param object $service
	 * @param string $className
	 */
	private function registerServiceToContainer($serviceId, $service, $className) {
		$this->container->set($serviceId, $service);
		$this->autoServiceCollector->addService($serviceId, $className);
	}

	/**
	 * @param string $className
	 * @return object
	 */
	private function createServiceByClassName($className) {
		if (!$this->classResolver->canBeResolved($className)) {
			throw new \SS6\AutoServicesBundle\Compiler\Exception\ServiceClassNotFoundException($className);
		}

		$reflectionClass = new ReflectionClass($className);
		$constructor = $reflectionClass->getConstructor();

		if ($constructor === null) {
			$service = new $className();
		} else {
			$service = $reflectionClass->newInstanceArgs($this->getConstructorArguments($constructor));
		}

		return $service;
	}

	/**
	 * @param \ReflectionFunctionAbstract $constructor
	 * @return array
	 */
	private function getConstructorArguments(\ReflectionFunctionAbstract $constructor) {
		$arguments = [];
		foreach ($constructor->getParameters() as $parameter) {
			/* @var $parameter \ReflectionParameter */
			if ($parameter->isDefaultValueAvailable()) {
				$arguments[] = $parameter->getDefaultValue();
			} else {
				$argumentClassName = $parameter->getClass()->name;
				$arguments[] = $this->findServiceByClassName($argumentClassName);
			}
		}
		return $arguments;
	}

	public function addScope(\Symfony\Component\DependencyInjection\ScopeInterface $scope) {
		$this->container->addScope($scope);
	}

	public function enterScope($name) {
		$this->container->enterScope($name);
	}

	public function getParameter($name) {
		return $this->container->getParameter($name);
	}

	public function has($id) {
		try {
			$this->get($id, self::EXCEPTION_ON_INVALID_REFERENCE);
			return true;
		} catch (\Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException $e) {
			return false;
		}
	}

	public function hasParameter($name) {
		return $this->container->hasParameter($name);
	}

	public function hasScope($name) {
		return $this->container->hasScope($name);
	}

	public function isScopeActive($name) {
		return $this->container->isScopeActive($name);
	}

	public function leaveScope($name) {
		$this->container->leaveScope($name);
	}

	public function set($id, $service, $scope = self::SCOPE_CONTAINER) {
		$this->container->set($id, $service, $scope);
	}

	public function setParameter($name, $value) {
		$this->container->setParameter($name, $value);
	}

}
