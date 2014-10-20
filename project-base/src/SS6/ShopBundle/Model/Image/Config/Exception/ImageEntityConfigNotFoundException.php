<?php

namespace SS6\ShopBundle\Model\Image\Config\Exception;

use Exception;

class ImageEntityConfigNotFoundException extends Exception implements ImageConfigException {

	/**
	 * @var string
	 */
	private $entityClassOrName;

	/**
	 * @param string $entityClassOrName
	 * @param \Exception $previous
	 */
	public function __construct($entityClassOrName, Exception $previous = null) {
		$this->entityClassOrName = $entityClassOrName;

		$message = sprintf('Not found image config for entity "%s".', $this->entityClassOrName);
		parent::__construct($message, 0, $previous);
	}

	/**
	 * @return string
	 */
	public function getEntityClassOrName() {
		return $this->entityClassOrName;
	}

}
