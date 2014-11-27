<?php

namespace SS6\ShopBundle\Model\Order\Status\Exception;

use Exception;
use SS6\ShopBundle\Component\Debug;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderStatusNotFoundException extends NotFoundHttpException implements OrderStatusException {

	/**
	 * @param mixed $criteria
	 * @param \Exception $previous
	 */
	public function __construct($criteria, Exception $previous = null) {
		parent::__construct('Order status not found by criteria ' . Debug::export($criteria), $previous, 0);
	}

}
