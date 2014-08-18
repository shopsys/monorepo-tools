<?php

namespace SS6\AutoServicesBundle\Compiler;

class AutoServicesCollector {

	const CONFIG_FILENAME = 'autoServices.json';
	const CONTAINER_INVALIDATOR_FILENAME = 'autoServicesContainerInvalidator';

	/**
	 * @var string
	 */
	private $cacheDir;

	/**
	 * @var string[]|null
	 */
	private $classesByServiceId;

	public function __construct($cacheDir) {
		$this->cacheDir = $cacheDir;
	}

	/**
	 * @return string
	 */
	public function getContainerInvalidatorFilepath() {
		return $this->cacheDir . DIRECTORY_SEPARATOR . self::CONTAINER_INVALIDATOR_FILENAME;
	}

	/**
	 * @return string
	 */
	private function getConfigFilepath() {
		return $this->cacheDir . DIRECTORY_SEPARATOR . self::CONFIG_FILENAME;
	}

	/**
	 * @return string[][]
	 */
	public function getServicesClassesIndexedByServiceId() {
		$this->load();
		return $this->classesByServiceId;
	}

	/**
	 * @param string $serviceId
	 * @param string $className
	 */
	public function addService($serviceId, $className) {
		$this->load();
		$this->classesByServiceId[$serviceId] = $className;
		$this->flush();
		$this->invalidateContainer();
	}

	/**
	 * @param string[] $classesByServiceId
	 */
	public function setServices(array $classesByServiceId) {
		$this->classesByServiceId = $classesByServiceId;
		$this->flush();
	}

	private function invalidateContainer() {
		file_put_contents($this->getContainerInvalidatorFilepath(), time());
	}

	private function flush() {
		$jsonConfig = json_encode($this->classesByServiceId, JSON_PRETTY_PRINT);
		file_put_contents($this->getConfigFilepath(), $jsonConfig);
	}

	private function load() {
		if ($this->classesByServiceId === null) {
			if (file_exists($this->getConfigFilepath())) {
				$jsonConfig = file_get_contents($this->getConfigFilepath());
				$this->classesByServiceId = json_decode($jsonConfig, true);
			}

			if (!is_array($this->classesByServiceId)) {
				$this->classesByServiceId = [];
			}
		}
	}

}
