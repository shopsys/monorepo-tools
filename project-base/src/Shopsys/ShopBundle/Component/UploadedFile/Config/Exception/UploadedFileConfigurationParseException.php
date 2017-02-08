<?php

namespace Shopsys\ShopBundle\Component\UploadedFile\Config\Exception;

use Exception;
use Shopsys\ShopBundle\Component\UploadedFile\Config\Exception\UploadedFileConfigException;

class UploadedFileConfigurationParseException extends Exception implements UploadedFileConfigException {

	/**
	 * @var string
	 */
	private $entityClass;

	/**
	 * @param string $entityClass
	 * @param \Exception|null $previous
	 */
	public function __construct($entityClass, Exception $previous = null) {
		$this->entityClass = $entityClass;

		$message = sprintf('Parsing of config entity class "%s" failed.', $this->entityClass);
		parent::__construct($message, 0, $previous);
	}

	/**
	 * @return string
	 */
	public function getEntityClass() {
		return $this->entityClass;
	}
}
