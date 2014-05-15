<?php

namespace SS6\ShopBundle\Model\Customer\Exception;

use Exception;

class UserNotFoundException extends Exception implements CustomerException {
	
	/**
	 * @param mixed $criteria
	 * @param \Exception $previous
	 */
	public function __construct($criteria, Exception $previous = null) {
		parent::__construct('User not found by criteria ' . var_export($criteria, true), 0, $previous);
	}
	
}
