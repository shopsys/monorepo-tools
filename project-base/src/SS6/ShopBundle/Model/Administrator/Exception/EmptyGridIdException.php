<?php

namespace SS6\ShopBundle\Model\Administrator\Exception;

use Exception;

class EmptyGridIdException extends Exception implements AdministratorException {

	/**
	 * @param string $message
	 * @param Exception $previous
	 */
	public function __construct($message = '', $previous = null) {
		parent::__construct($message, 0, $previous);
	}
}
