<?php

namespace SS6\ShopBundle\Tests\Test\Codeception\Exception;

use Exception;

class DeprecatedMethodException extends Exception {

	/**
	 * @param string $message
	 * @param \Exception|null $previous
	 */
	public function __construct($message = '', Exception $previous = null) {
		parent::__construct($message, 0, $previous);
	}
}
