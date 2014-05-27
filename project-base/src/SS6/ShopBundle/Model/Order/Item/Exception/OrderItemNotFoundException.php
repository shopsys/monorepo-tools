<?php

namespace SS6\ShopBundle\Model\Order\Item\Exception;

use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderItemNotFoundException extends NotFoundHttpException implements OrderItemException {
	
	/**
	 * @param mixed $criteria
	 * @param \Exception $previous
	 */
	public function __construct($criteria, Exception $previous = null) {
		parent::__construct('Order item not found by criteria ' . var_export($criteria, true), $previous, 0);
	}
	
}
