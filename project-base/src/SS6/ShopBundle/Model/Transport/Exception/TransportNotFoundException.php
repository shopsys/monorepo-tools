<?php

namespace SS6\ShopBundle\Model\Transport\Exception;

use Exception;

class TransportNotFoundException extends Exception implements TransportException {
	
	/**
	 * @param mixed $criteria
	 * @param \Exception $previous
	 */
	public function __construct($criteria, Exception $previous = null) {
		parent::__construct('Transport not found by criteria ' . var_export($criteria, true), 0, $previous);
	}
	
}
