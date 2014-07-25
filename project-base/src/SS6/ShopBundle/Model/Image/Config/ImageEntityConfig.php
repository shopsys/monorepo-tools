<?php

namespace SS6\ShopBundle\Model\Image\Config;

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
	private $types;

	/**
	 * @var \SS6\ShopBundle\Model\Image\Config\ImageSizeConfig[]
	 */
	private $sizes;

	/**
	 *
	 * @param string $entityName
	 * @param string $entityClass
	 * @param array $types
	 * @param \SS6\ShopBundle\Model\Image\Config\ImageSizeConfig[] $sizes
	 */
	public function __construct($entityName, $entityClass, $types, array $sizes) {
		$this->entityName = $entityName;
		$this->entityClass = $entityClass;
		$this->types = $types;
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
	 * @return array
	 */
	public function getTypes() {
		return $this->types;
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
		if (array_key_exists($type, $this->types)) {
			return $this->types[$type];
		} else {
			throw new \SS6\ShopBundle\Model\Image\Config\Exception\ImageTypeNotFoundException($this->entityClass, $type);
		}
	}

	/**
	 * @param string $sizeName
	 * @return \SS6\ShopBundle\Model\Image\Config\ImageSizeConfig
	 */
	public function getSize($sizeName = null) {
		return $this->getSizeFromSizes($this->sizes, $sizeName);
	}

	/**
	 * @param string|null $type
	 * @param string|null $sizeName
	 * @return \SS6\ShopBundle\Model\Image\Config\ImageSizeConfig
	 */
	public function getTypeSize($type = null, $sizeName = null) {
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
	private function getSizeFromSizes($sizes, $sizeName = null) {
		$key = $sizeName !== null ? $sizeName : self::WITHOUT_NAME_KEY;
		if (array_key_exists($key, $sizes)) {
			return $sizes[$sizeName];
		} else {
			throw new \SS6\ShopBundle\Model\Image\Config\Exception\ImageSizeNotFoundException($this->entityClass, $sizeName);
		}
	}

}
