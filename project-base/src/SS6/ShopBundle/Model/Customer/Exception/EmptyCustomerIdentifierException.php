<?php

namespace SS6\ShopBundle\Model\Customer\Exception;

use Exception;

class EmptyCustomerIdentifierException extends Exception implements CustomerException {

	/**
	 * @param string $message
	 * @param Exception $previous
	 */
	public function __construct($message = '', $previous = null) {
		parent::__construct($message, 0, $previous);
	}

}
