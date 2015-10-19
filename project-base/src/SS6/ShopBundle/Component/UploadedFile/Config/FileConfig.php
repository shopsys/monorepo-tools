<?php

namespace SS6\ShopBundle\Component\UploadedFile\Config;

class FileConfig {

	/**
	 * @var \SS6\ShopBundle\Component\UploadedFile\Config\FileEntityConfig[]
	 */
	private $fileEntityConfigsByClass;

	/**
	 * @param \SS6\ShopBundle\Component\UploadedFile\Config\FileEntityConfig[] $fileEntityConfigsByClass
	 */
	public function __construct(array $fileEntityConfigsByClass) {
		$this->fileEntityConfigsByClass = $fileEntityConfigsByClass;
	}

	/**
	 * @param Object $entity
	 * @return string
	 */
	public function getEntityName($entity) {
		return $this->getFileEntityConfig($entity)->getEntityName();
	}

	/**
	 * @param Object $entity
	 * @return \SS6\ShopBundle\Component\UploadedFile\Config\FileEntityConfig
	 */
	public function getFileEntityConfig($entity) {
		foreach ($this->fileEntityConfigsByClass as $className => $entityConfig) {
			if ($entity instanceof $className) {
				return $entityConfig;
			}
		}

		throw new \SS6\ShopBundle\Component\UploadedFile\Config\Exception\FileConfigDataNotFoundException(
			$entity ? get_class($entity) : null
		);
	}

	/**
	 * @param object $entity
	 * @return bool
	 */
	public function hasFileConfig($entity) {
		foreach ($this->fileEntityConfigsByClass as $className => $entityConfig) {
			if ($entity instanceof $className) {
				return true;
			}
		}
		return false;
	}

	/**
	 * @param string $entityName
	 * @return \SS6\ShopBundle\Component\UploadedFile\Config\FileEntityConfig;
	 */
	public function getEntityFileConfigByEntityName($entityName) {
		foreach ($this->fileEntityConfigsByClass as $entityConfig) {
			if ($entityConfig->getEntityName() === $entityName) {
				return $entityConfig;
			}
		}

		throw new \SS6\ShopBundle\Component\UploadedFile\Config\Exception\FileConfigDataNotFoundException($entityName);
	}

	/**
	 * @param string $class
	 * @return \SS6\ShopBundle\Component\UploadedFile\Config\FileEntityConfig
	 */
	public function getFileConfigDataByClass($class) {
		if (array_key_exists($class, $this->fileEntityConfigsByClass)) {
			return $this->fileEntityConfigsByClass[$class];
		}

		throw new \SS6\ShopBundle\Component\UploadedFile\Config\Exception\FileConfigDataNotFoundException($class);
	}

	/**
	 * @return \SS6\ShopBundle\Component\UploadedFile\Config\FileEntityConfig[]
	 */
	public function getAllFileEntityConfigs() {
		return $this->fileEntityConfigsByClass;
	}

}
