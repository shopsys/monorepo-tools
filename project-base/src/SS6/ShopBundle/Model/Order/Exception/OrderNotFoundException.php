<?php

namespace SS6\ShopBundle\Model\Order\Exception;

use Exception;

class OrderNotFoundException extends Exception implements OrderException {
	
	/**
	 * @param mixed $criteria
	 * @param \Exception $previous
	 */
	public function __construct($criteria, Exception $previous = null) {
		parent::__construct('Order not found by criteria ' . var_export($criteria, true), 0, $previous);
	}
	
}
