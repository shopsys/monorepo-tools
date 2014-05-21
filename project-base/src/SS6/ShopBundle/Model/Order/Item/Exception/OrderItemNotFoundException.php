<?php

namespace SS6\ShopBundle\Model\Order\Item\Exception;

use Exception;

class OrderItemNotFoundException extends Exception implements OrderItemException {
	
	/**
	 * @param mixed $criteria
	 * @param \Exception $previous
	 */
	public function __construct($criteria, Exception $previous = null) {
		parent::__construct('Order item not found by criteria ' . var_export($criteria, true), 0, $previous);
	}
	
}
