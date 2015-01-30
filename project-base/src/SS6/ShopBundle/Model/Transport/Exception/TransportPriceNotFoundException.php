<?php

namespace SS6\ShopBundle\Model\Transport\Exception;

use Exception;
use SS6\ShopBundle\Component\Debug;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TransportPriceNotFoundException extends NotFoundHttpException implements TransportException {

	/**
	 * @param Currency $currency
	 * @param \Exception $previous
	 */
	public function __construct($currency, Exception $previous = null) {
		parent::__construct('Transport price not found by currency ' . Debug::export($currency), $previous, 0);
	}
}
