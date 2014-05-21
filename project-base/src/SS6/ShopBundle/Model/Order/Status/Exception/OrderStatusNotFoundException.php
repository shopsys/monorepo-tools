<?php

namespace SS6\ShopBundle\Model\Order\Status\Exception;

use Exception;

class OrderStatusNotFoundException extends Exception implements OrderStatusException {
	
	/**
	 * @param mixed $criteria
	 * @param \Exception $previous
	 */
	public function __construct($criteria, Exception $previous = null) {
		parent::__construct('Order status not found by criteria ' . var_export($criteria, true), 0, $previous);
	}
	
}
