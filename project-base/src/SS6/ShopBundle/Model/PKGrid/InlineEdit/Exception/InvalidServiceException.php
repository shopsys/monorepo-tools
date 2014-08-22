<?php

namespace SS6\ShopBundle\Model\PKGrid\InlineEdit\Exception;

use Exception;

class InvalidServiceException extends Exception implements InlineEditException {

	/**
	 * @var string
	 */
	private $serviceName;

	/**
	 * @param string $serviceName
	 * @param \Exception $previous
	 */
	public function __construct($serviceName, Exception $previous = null) {
		$this->serviceName = $serviceName;
		$message = 'Service with name "' . $this->serviceName . '" does not exists or not implement necessary interface.';
		parent::__construct($message, 0, $previous);
	}

	/**
	 * @return string
	 */
	public function getServiceName() {
		return $this->serviceName;
	}
	
}
