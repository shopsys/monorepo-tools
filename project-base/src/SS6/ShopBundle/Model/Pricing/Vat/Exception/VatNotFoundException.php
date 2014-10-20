<?php

namespace SS6\ShopBundle\Model\Pricing\Vat\Exception;

use Exception;
use SS6\ShopBundle\Model\Pricing\Vat\Exception\VatException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VatNotFoundException extends NotFoundHttpException implements VatException {

	/**
	 * @param mixed $criteria
	 * @param \Exception $previous
	 */
	public function __construct($criteria, Exception $previous = null) {
		parent::__construct('Vat not found by criteria ' . var_export($criteria, true), $previous, 0);
	}

}
