<?php

namespace SS6\ShopBundle\Model\Image\Config;

use SS6\ShopBundle\Model\Image\Image;

class ImageConfig {

	const ORIGINAL_SIZE_NAME = 'original';
	const DEFAULT_SIZE_NAME = 'default';

	/**
	 * @var \SS6\ShopBundle\Model\Image\Config\ImageEntityConfig[]
	 */
	private $imageEntityConfigsByClass;

	/**
	 * @param \SS6\ShopBundle\Model\Image\Config\ImageEntityConfig[] $imageEntityConfigsByClass
	 */
	public function __construct(array $imageEntityConfigsByClass) {
		$this->imageEntityConfigsByClass = $imageEntityConfigsByClass;
	}

	/**
	 * @param Object $entity
	 * @return string
	 */
	public function getEntityName($entity) {
		$entityConfig = $this->getImageEntityConfig($entity);
		return $entityConfig->getEntityName();
	}

	/**
	 * @param Object $entity
	 * @param string|null $type
	 * @param string|null $sizeName
	 * @return \SS6\ShopBundle\Model\Image\Config\ImageSizeConfig
	 */
	public function getImageSizeConfigByEntity($entity, $type, $sizeName) {
		$entityConfig = $this->getImageEntityConfig($entity);
		return $entityConfig->getSizeConfigByType($type, $sizeName);
	}

	/**
	 * @param string $entityName
	 * @param string|null $type
	 * @param string|null $sizeName
	 * @return \SS6\ShopBundle\Model\Image\Config\ImageSizeConfig
	 */
	public function getImageSizeConfigByEntityName($entityName, $type, $sizeName) {
		$entityConfig = $this->getEntityConfigByEntityName($entityName);
		return $entityConfig->getSizeConfigByType($type, $sizeName);
	}

	/**
	 * @param string $entityName
	 * @param string|null $type
	 * @param string|null $sizeName
	 */
	public function assertImageSizeConfigByEntityNameExists($entityName, $type, $sizeName) {
		$this->getImageSizeConfigByEntityName($entityName, $type, $sizeName);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Image\Image $image
	 * @param string|null $sizeName
	 * @return \SS6\ShopBundle\Model\Image\Config\ImageSizeConfig
	 */
	public function getImageSizeConfigByImage(Image $image, $sizeName) {
		$entityConfig = $this->getEntityConfigByEntityName($image->getEntityName());
		return $entityConfig->getSizeConfigByType($image->getType(), $sizeName);
	}

	/**
	 * @param Object $entity
	 * @return \SS6\ShopBundle\Model\Image\Config\ImageEntityConfig
	 */
	public function getImageEntityConfig($entity) {
		foreach ($this->imageEntityConfigsByClass as $className => $entityConfig) {
			if ($entity instanceof $className) {
				return $entityConfig;
			}
		}

		throw new \SS6\ShopBundle\Model\Image\Config\Exception\ImageEntityConfigNotFoundException($className);
	}

	/**
	 * @param object $entity
	 * @return boolean
	 */
	public function hasImageConfig($entity) {
		foreach ($this->imageEntityConfigsByClass as $className => $entityConfig) {
			if ($entity instanceof $className) {
				return true;
			}
		}
		return false;
	}

	/**
	 * @param string $entityName
	 * @return \SS6\ShopBundle\Model\Image\Config\ImageEntityConfig;
	 */
	public function getEntityConfigByEntityName($entityName) {
		foreach ($this->imageEntityConfigsByClass as $entityConfig) {
			if ($entityConfig->getEntityName() === $entityName) {
				return $entityConfig;
			}
		}

		throw new \SS6\ShopBundle\Model\Image\Config\Exception\ImageEntityConfigNotFoundException($entityName);
	}

	/**
	 * @return \SS6\ShopBundle\Model\Image\Config\ImageEntityConfig[]
	 */
	public function getAllImageEntityConfigsByClass() {
		return $this->imageEntityConfigsByClass;
	}

}
