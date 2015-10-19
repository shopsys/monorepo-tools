<?php

namespace SS6\ShopBundle\Component\UploadedFile\Config\Exception;

use Exception;
use SS6\ShopBundle\Component\UploadedFile\Config\Exception\UploadedFileConfigException;

class DuplicateEntityNameException extends Exception implements UploadedFileConfigException {

	/**
	 * @var string
	 */
	private $entityName;

	/**
	 * @param string $entityName
	 * @param \Exception $previous
	 */
	public function __construct($entityName, Exception $previous = null) {
		$this->entityName = $entityName;

		$message = sprintf('File entity name "%s" is not unique.', $this->entityName);
		parent::__construct($message, 0, $previous);
	}

	/**
	 * @return string
	 */
	public function getEntityName() {
		return $this->entityName;
	}
}
