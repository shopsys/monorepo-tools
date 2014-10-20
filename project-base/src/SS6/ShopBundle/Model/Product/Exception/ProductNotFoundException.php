<?php

namespace SS6\ShopBundle\Model\Product\Exception;

use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductNotFoundException extends NotFoundHttpException implements ProductException {

	/**
	 * @param mixed $criteria
	 * @param \Exception $previous
	 */
	public function __construct($criteria, Exception $previous = null) {
		parent::__construct('Product not found by criteria ' . var_export($criteria, true), $previous, 0);
	}

}
