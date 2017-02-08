<?php

namespace Shopsys\AutoServicesBundle\Compiler;

use ReflectionClass;

class ContainerClassList {

	/**
	 * @var string
	 */
	private $cacheFilepath;

	/**
	 * @var string[][]
	 */
	private $serviceIdsByClass;

	public function __construct($cacheDir) {
		$this->cacheFilepath = $cacheDir . '/servicesByClass.json';
	}

	public function load() {
		if ($this->serviceIdsByClass === null) {
			if (file_exists($this->cacheFilepath)) {
				$json = file_get_contents($this->cacheFilepath);
				$this->serviceIdsByClass = json_decode($json, true);
			}

			if (!is_array($this->serviceIdsByClass)) {
				$this->serviceIdsByClass = [];
			}
		}
	}

	public function clean() {
		$this->serviceIdsByClass = [];
		if (file_exists($this->cacheFilepath)) {
			unlink($this->cacheFilepath);
		}
	}

	public function save() {
		if ($this->serviceIdsByClass !== null) {
			foreach ($this->serviceIdsByClass as $class => $serviceIds) {
				$this->serviceIdsByClass[$class] = array_unique($this->serviceIdsByClass[$class]);
			}
			file_put_contents($this->cacheFilepath, json_encode($this->serviceIdsByClass, JSON_PRETTY_PRINT));
		}
	}

	/**
	 * @return string[]
	 */
	public function getServicesIdsIndexedByClass() {
		$this->load();
		return $this->serviceIdsByClass;
	}

	/**
	 * @param string $class
	 * @return string
	 */
	public function getServiceIdByClass($class) {
		$this->load();

		if (!$this->hasClass($class)) {
			throw new \Shopsys\AutoServicesBundle\Compiler\Exception\ServiceClassNotFoundException($class);
		}
		$serviceIds = array_unique($this->serviceIdsByClass[$class]);
		if (count($serviceIds) !== 1) {
			throw new \Shopsys\AutoServicesBundle\Compiler\Exception\MultipleServicesOfClassException($class, $serviceIds);
		}

		return array_pop($serviceIds);
	}

	/**
	 * @param string $class
	 * @return bool
	 */
	public function hasClass($class) {
		$this->load();
		return array_key_exists($class, $this->serviceIdsByClass);
	}

	/**
	 * @param string $serviceId
	 * @param string $class
	 */
	public function addClass($serviceId, $class) {
		$this->load();

		if (empty($class) || !is_string($class) || !$this->classOrInterfaceExists($class)) {
			return;
		}
		$reflection = new ReflectionClass($class);

		if (!$reflection) {
			return;
		}

		$this->serviceIdsByClass[$reflection->getName()][] = $serviceId;

		foreach ($reflection->getInterfaceNames() as $interface) {
			$this->serviceIdsByClass[$interface][] = $serviceId;
		}

		$parent = $reflection->getParentClass();
		while ($parent) {
			$this->serviceIdsByClass[$parent->getName()][] = $serviceId;
			$parent = $parent->getParentClass();
		}
	}

	/**
	 * @param string $classOrInterfaceName
	 * @return bool
	 */
	private function classOrInterfaceExists($classOrInterfaceName) {
		return class_exists($classOrInterfaceName) || interface_exists($classOrInterfaceName);
	}

}
