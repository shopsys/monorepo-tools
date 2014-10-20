<?php

namespace SS6\ShopBundle\Model\Cart\Exception;

use Exception;

class InvalidCartItemException extends Exception implements CartException {

	/**
	 * @param string $message
	 * @param Exception $previous
	 */
	public function __construct($message = null, $previous = null) {
		parent::__construct($message, 0, $previous);
	}
}
