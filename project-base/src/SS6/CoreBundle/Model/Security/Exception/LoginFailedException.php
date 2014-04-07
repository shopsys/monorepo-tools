<?php

namespace SS6\CoreBundle\Model\Security\Exception;

use Exception;

class LoginFailedException extends Exception implements SecurityException {
	
	/**
	 * @param string $message
	 * @param integer $code
	 */
	public function __construct($message, $code = 0) {
		parent::__construct($message, $code, null);
	}
}
