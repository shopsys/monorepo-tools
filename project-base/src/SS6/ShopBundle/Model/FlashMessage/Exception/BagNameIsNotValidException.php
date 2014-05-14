<?php

namespace SS6\ShopBundle\Model\FlashMessage\Exception;

use Exception;

class BagNameIsNotValidException extends Exception implements FlashMessageException {
	
	/**
	 * @param string $message
	 * @param Exception $previous
	 */
	public function __construct($message = null, $previous = null) {
		parent::__construct($message, 0, $previous);
	}
}