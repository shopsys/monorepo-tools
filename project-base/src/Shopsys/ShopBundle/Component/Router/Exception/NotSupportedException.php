<?php

namespace SS6\ShopBundle\Component\Router\Exception;

use Exception;

class NotSupportedException extends Exception implements RouterException {

	/**
	 * @param string $message
	 * @param \Exception|null $previous
	 */
	public function __construct($message = '', Exception $previous = null) {
		parent::__construct($message, 0, $previous);
	}
}
