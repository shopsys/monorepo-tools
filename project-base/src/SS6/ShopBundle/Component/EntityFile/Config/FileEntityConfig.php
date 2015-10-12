<?php

namespace SS6\ShopBundle\Component\EntityFile\Config;

class FileEntityConfig {

	/**
	 * @var string
	 */
	private $entityName;

	/**
	 * @var string
	 */
	private $entityClass;

	/**
	 * @param string $entityName
	 * @param string $entityClass
	 */
	public function __construct($entityName, $entityClass) {
		$this->entityName = $entityName;
		$this->entityClass = $entityClass;
	}

	/**
	 * @return string
	 */
	public function getEntityName() {
		return $this->entityName;
	}

	/**
	 * @return string
	 */
	public function getEntityClass() {
		return $this->entityClass;
	}

}
