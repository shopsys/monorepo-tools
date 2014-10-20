<?php

namespace SS6\ShopBundle\Model\Order\Status\Exception;

use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderStatusNotFoundException extends NotFoundHttpException implements OrderStatusException {

	/**
	 * @param mixed $criteria
	 * @param \Exception $previous
	 */
	public function __construct($criteria, Exception $previous = null) {
		parent::__construct('Order status not found by criteria ' . var_export($criteria, true), $previous, 0);
	}

}
