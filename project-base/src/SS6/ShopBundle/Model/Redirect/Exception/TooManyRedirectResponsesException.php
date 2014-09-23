<?php

namespace SS6\ShopBundle\Model\Redirect\Exception;

use Exception;

class TooManyRedirectResponsesException extends Exception implements RedirectException {

	/**
	 * @param string $message
	 * @param \Exception $previous
	 */
	public function __construct($message = null, $previous = null) {
		parent::__construct($message, 0, $previous);
	}
}