<?php

namespace SS6\ShopBundle\Model\Payment\Exception;

use Exception;
use SS6\ShopBundle\Component\Debug;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaymentNotFoundException extends NotFoundHttpException implements PaymentException {

	/**
	 * @param mixed $criteria
	 * @param \Exception $previous
	 */
	public function __construct($criteria, Exception $previous = null) {
		parent::__construct('Payment not found by criteria ' . Debug::export($criteria), $previous, 0);
	}

}
