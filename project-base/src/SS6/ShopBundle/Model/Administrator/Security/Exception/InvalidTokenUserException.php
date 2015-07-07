<?php

namespace SS6\ShopBundle\Model\Administrator\Security\Exception;

use Exception;
use SS6\ShopBundle\Model\Administrator\Security\Exception\SecurityException;

class InvalidTokenUserException extends Exception implements SecurityException {

	/**
	 * @param string $message
	 * @param \Exception $previous
	 */
	public function __construct($message = '', Exception $previous = null) {
		parent::__construct($message, 0, $previous);
	}

}
