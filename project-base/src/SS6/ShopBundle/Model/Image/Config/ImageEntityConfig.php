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
	private $filenameMethodsByType;

	/**
	 * @var array
	 */
	private $sizesByType;

	/**
	 * @var \SS6\ShopBundle\Model\Image\Config\ImageSizeConfig[]
	 */
	private $sizes;

	/**
	 *
	 * @param string $entityName
	 * @param string $entityClass
	 * @param array $filenameMethodsByType
	 * @param array $sizesByType
	 * @param \SS6\ShopBundle\Model\Image\Config\ImageSizeConfig[] $sizes
	 */
	public function __construct($entityName, $entityClass, array $filenameMethodsByType, array $sizesByType, array $sizes) {
		$this->entityName = $entityName;
		$this->entityClass = $entityClass;
		$this->filenameMethodsByType = $filenameMethodsByType;
		$this->sizesByType = $sizesByType;
		$this->sizes = $sizes;
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
	 * @return string
	 */
	public function getFilenameMethodByType($type) {
		$key = Condition::ifNull($type, self::WITHOUT_NAME_KEY);
		if (array_key_exists($key, $this->filenameMethodsByType)) {
			return $this->filenameMethodsByType[$key];
		} else {
			throw new \SS6\ShopBundle\Model\Image\Config\Exception\ImageTypeNotFoundException($this->entityClass, $type);
		}
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
	 * @throws \SS6\ShopBundle\Model\Image\Config\Exception\ImageTypeNotFoundException
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
	 * @param \SS6\ShopBundle\Model\Image\Config\ImageSizeConfig[] $sizes
	 * @param string $sizeName
	 * @return \SS6\ShopBundle\Model\Image\Config\ImageSizeConfig
	 * @throws \SS6\ShopBundle\Model\Image\Config\Exception\ImageSizeNotFoundException
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
