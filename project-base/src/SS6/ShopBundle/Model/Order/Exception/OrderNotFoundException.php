<?php

namespace SS6\ShopBundle\Model\Order\Exception;

use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderNotFoundException extends NotFoundHttpException implements OrderException {

	/**
	 * @param mixed $criteria
	 * @param \Exception $previous
	 */
	public function __construct($criteria, Exception $previous = null) {
		parent::__construct('Order not found by criteria ' . var_export($criteria, true), $previous, 0);
	}

}
