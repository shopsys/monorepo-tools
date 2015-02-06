<?php

namespace SS6\ShopBundle\Component\String\Exception;

use Exception;

class HashGenerationFailedException extends Exception implements StringException {

	/**
	 * @param string|null $message
	 * @param \Exception $previous
	 */
	public function __construct($message = null, Exception $previous = null) {
		parent::__construct($message, 0, $previous);
	}

}
