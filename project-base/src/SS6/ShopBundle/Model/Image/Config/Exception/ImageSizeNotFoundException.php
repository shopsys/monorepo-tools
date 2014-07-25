<?php

namespace SS6\ShopBundle\Model\Image\Config\Exception;

use Exception;

class ImageSizeNotFoundException extends Exception implements ImageConfigException {

	/**
	 * @var string
	 */
	private $entityClass;

	/**
	 * @var string
	 */
	private $sizeName;

	/**
	 * @param string $entityClass
	 * @param string $sizeName
	 * @param \Exception $previous
	 */
	public function __construct($entityClass, $sizeName, Exception $previous = null) {
		$this->entityClass = $entityClass;
		$this->sizeName = $sizeName;

		$message = sprintf('Image size "%s" not found for entity "%s".', $this->sizeName, $this->entityClass);
		parent::__construct($message, 0, $previous);
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
	public function getSizeName() {
		return $this->sizeName;
	}

}