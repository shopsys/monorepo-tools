<?php

namespace SS6\ShopBundle\Component\UploadedFile\Config\Exception;

use Exception;
use SS6\ShopBundle\Component\UploadedFile\Config\Exception\FileConfigException;

class FileConfigDataNotFoundException extends Exception implements FileConfigException {

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

		parent::__construct('Not found file config for entity "' . $entityClassOrName . '".', 0, $previous);
	}

	/**
	 * @return string
	 */
	public function getEntityClassOrName() {
		return $this->entityClassOrName;
	}

}
