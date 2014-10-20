<?php

namespace SS6\ShopBundle\Model\Payment\Exception;

use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaymentNotFoundException extends NotFoundHttpException implements PaymentException {

	/**
	 * @param mixed $criteria
	 * @param \Exception $previous
	 */
	public function __construct($criteria, Exception $previous = null) {
		parent::__construct('Payment not found by criteria ' . var_export($criteria, true), $previous, 0);
	}

}
