<?php

namespace SS6\ShopBundle\Model\Domain\Exception;

use Exception;

class NoDomainSelectedException extends Exception implements DomainException {
	
	/**
	 * @param string $message
	 * @param Exception $previous
	 */
	public function __construct($message = null, $previous = null) {
		parent::__construct($message, 0, $previous);
	}
}