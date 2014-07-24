<?php

namespace SS6\ShopBundle\Model\Image\Config\Exception;

use Exception;

class EntityParseException extends Exception implements ImageConfigException {

	/**
	 * @var string
	 */
	private $entityClass;

	/**
	 * @param string $entityClass
	 * @param \Exception $previous
	 */
	public function __construct($entityClass, Exception $previous = null) {
		$this->entityClass = $entityClass;

		$message = sprintf('Parse config entity class "%s" failed.', $this->entityClass);
		parent::__construct($message, 0, $previous);
	}

	/**
	 * @return string
	 */
	public function getEntityClass() {
		return $this->entityClass;
	}
}