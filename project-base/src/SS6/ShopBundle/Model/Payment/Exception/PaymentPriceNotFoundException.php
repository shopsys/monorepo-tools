<?php

namespace SS6\ShopBundle\Model\Payment\Exception;

use Exception;
use SS6\ShopBundle\Component\Debug;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaymentPriceNotFoundException extends NotFoundHttpException implements PaymentException {

	/**
	 * @param Currency $currency
	 * @param \Exception $previous
	 */
	public function __construct($currency, Exception $previous = null) {
		parent::__construct('Payment price not found by currency ' . Debug::export($currency), $previous, 0);
	}
}
