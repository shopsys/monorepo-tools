<?php

namespace SS6\ShopBundle\Model\Image\Config;

use SS6\ShopBundle\Component\Condition;

class ImageEntityConfig {

	const WITHOUT_NAME_KEY = '__NULL__';

	/**
	 * @var string
	 */
	private $entityName;

	/**
	 * @var string
	 */
	private $entityClass;

	/**
	 * @var array
	 */
	private $sizesByType;

	/**
	 * @var \SS6\ShopBundle\Model\Image\Config\ImageSizeConfig[]
	 */
	private $sizes;

	/**
	 * @var array
	 */
	private $multipleByType;

	/**
	 *
	 * @param string $entityName
	 * @param string $entityClass
	 * @param array $sizesByType
	 * @param \SS6\ShopBundle\Model\Image\Config\ImageSizeConfig[] $sizes
	 * @param array $multipleByType
	 */
	public function __construct($entityName, $entityClass, array $sizesByType, array $sizes, array $multipleByType) {
		$this->entityName = $entityName;
		$this->entityClass = $entityClass;
		$this->sizesByType = $sizesByType;
		$this->sizes = $sizes;
		$this->multipleByType = $multipleByType;
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

	/**
	 * @return array
	 */
	public function getTypes() {
		return array_keys($this->sizesByType);
	}

	/**
	 * @return \SS6\ShopBundle\Model\Image\Config\ImageSizeConfig[]
	 */
	public function getSizes() {
		return $this->sizes;
	}

	/**
	 * @param string $type
	 * @return \SS6\ShopBundle\Model\Image\Config\ImageSizeConfig[]
	 */
	public function getTypeSizes($type) {
		if (array_key_exists($type, $this->sizesByType)) {
			return $this->sizesByType[$type];
		} else {
			throw new \SS6\ShopBundle\Model\Image\Config\Exception\ImageTypeNotFoundException($this->entityClass, $type);
		}
	}

	/**
	 * @param string|null $sizeName
	 * @return \SS6\ShopBundle\Model\Image\Config\ImageSizeConfig
	 */
	public function getSize($sizeName) {
		return $this->getSizeFromSizes($this->sizes, $sizeName);
	}

	/**
	 * @param string|null $type
	 * @param string|null $sizeName
	 * @return \SS6\ShopBundle\Model\Image\Config\ImageSizeConfig
	 */
	public function getTypeSize($type, $sizeName) {
		if ($type === null) {
			$typeSizes = $this->sizes;
		} else {
			$typeSizes = $this->getTypeSizes($type);
		}
		return $this->getSizeFromSizes($typeSizes, $sizeName);
	}

	/**
	 * @param string|null $type
	 * @return bool
	 */
	public function isMultiple($type) {
		$key = Condition::ifNull($type, self::WITHOUT_NAME_KEY);
		if (array_key_exists($key, $this->multipleByType)) {
			return $this->multipleByType[$key];
		} else {
			throw new \SS6\ShopBundle\Model\Image\Config\Exception\ImageTypeNotFoundException($this->entityClass, $type);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Image\Config\ImageSizeConfig[] $sizes
	 * @param string $sizeName
	 * @return \SS6\ShopBundle\Model\Image\Config\ImageSizeConfig
	 */
	private function getSizeFromSizes($sizes, $sizeName) {
		$key = Condition::ifNull($sizeName, self::WITHOUT_NAME_KEY);
		if (array_key_exists($key, $sizes)) {
			return $sizes[$key];
		} else {
			throw new \SS6\ShopBundle\Model\Image\Config\Exception\ImageSizeNotFoundException($this->entityClass, $sizeName);
		}
	}

}
