<?php

namespace SS6\ShopBundle\Model\Product\TopProduct\Exception;

use Exception;
use SS6\ShopBundle\Model\Product\TopProduct\Exception\TopProductException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TopProductNotFoundException extends NotFoundHttpException implements TopProductException {

	/**
	 * @param mixed $criteria
	 * @param \Exception $previous
	 */
	public function __construct($criteria, Exception $previous = null) {
		parent::__construct('Top product not found by criteria ' . var_export($criteria, true), $previous, 0);
	}

}
