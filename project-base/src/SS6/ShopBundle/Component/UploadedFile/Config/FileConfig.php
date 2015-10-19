<?php

namespace SS6\ShopBundle\Component\UploadedFile\Config;

class FileConfig {

	/**
	 * @var \SS6\ShopBundle\Component\UploadedFile\Config\UploadedFileEntityConfig[]
	 */
	private $uploadedFileEntityConfigsByClass;

	/**
	 * @param \SS6\ShopBundle\Component\UploadedFile\Config\UploadedFileEntityConfig[] $fileEntityConfigsByClass
	 */
	public function __construct(array $fileEntityConfigsByClass) {
		$this->uploadedFileEntityConfigsByClass = $fileEntityConfigsByClass;
	}

	/**
	 * @param Object $entity
	 * @return string
	 */
	public function getEntityName($entity) {
		return $this->getUploadedFileEntityConfig($entity)->getEntityName();
	}

	/**
	 * @param Object $entity
	 * @return \SS6\ShopBundle\Component\UploadedFile\Config\UploadedFileEntityConfig
	 */
	public function getUploadedFileEntityConfig($entity) {
		foreach ($this->uploadedFileEntityConfigsByClass as $className => $entityConfig) {
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
		foreach ($this->uploadedFileEntityConfigsByClass as $className => $entityConfig) {
			if ($entity instanceof $className) {
				return true;
			}
		}
		return false;
	}

	/**
	 * @param string $entityName
	 * @return \SS6\ShopBundle\Component\UploadedFile\Config\UploadedFileEntityConfig;
	 */
	public function getUploadedFileEntityConfigByEntityName($entityName) {
		foreach ($this->uploadedFileEntityConfigsByClass as $entityConfig) {
			if ($entityConfig->getEntityName() === $entityName) {
				return $entityConfig;
			}
		}

		throw new \SS6\ShopBundle\Component\UploadedFile\Config\Exception\FileConfigDataNotFoundException($entityName);
	}

	/**
	 * @param string $class
	 * @return \SS6\ShopBundle\Component\UploadedFile\Config\UploadedFileEntityConfig
	 */
	public function getUploadedFileEntityConfigDataByClass($class) {
		if (array_key_exists($class, $this->uploadedFileEntityConfigsByClass)) {
			return $this->uploadedFileEntityConfigsByClass[$class];
		}

		throw new \SS6\ShopBundle\Component\UploadedFile\Config\Exception\FileConfigDataNotFoundException($class);
	}

	/**
	 * @return \SS6\ShopBundle\Component\UploadedFile\Config\UploadedFileEntityConfig[]
	 */
	public function getAllUploadedFileEntityConfigs() {
		return $this->uploadedFileEntityConfigsByClass;
	}

}
