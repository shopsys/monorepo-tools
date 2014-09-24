<?php

namespace SS6\ShopBundle\Model\SubRequest\Exception;

use Exception;

class TooManyRedirectResponsesException extends Exception implements SubRequestException {

	/**
	 * @param string $message
	 * @param \Exception $previous
	 */
	public function __construct($message = null, $previous = null) {
		parent::__construct($message, 0, $previous);
	}
}