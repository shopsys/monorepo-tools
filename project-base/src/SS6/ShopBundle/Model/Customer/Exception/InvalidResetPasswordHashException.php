<?php

namespace SS6\ShopBundle\Model\Customer\Exception;

use Exception;

class InvalidResetPasswordHashException extends Exception implements CustomerException {

	/**
	 * @param string|null $message
	 * @param Exception $previous
	 */
	public function __construct($message = null, $previous = null) {
		parent::__construct($message, 0, $previous);
	}

}
