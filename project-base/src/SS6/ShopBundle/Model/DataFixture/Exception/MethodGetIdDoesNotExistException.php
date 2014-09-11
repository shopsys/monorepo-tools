<?php

namespace SS6\ShopBundle\Model\DataFixture\Exception;

use Exception;
use SS6\ShopBundle\Model\DataFixture\Exception\DataFixtureException;

class MethodGetIdDoesNotExistException extends Exception implements DataFixtureException {

	/**
	 * @param string $message
	 * @param \Exception $previous
	 */
	public function __construct($message, Exception $previous = null) {
		parent::__construct($message, 0, $previous);
	}

}