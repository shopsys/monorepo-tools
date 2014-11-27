<?php

namespace SS6\ShopBundle\Model\Product\Exception;

use Exception;
use SS6\ShopBundle\Component\Debug;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductNotFoundException extends NotFoundHttpException implements ProductException {

	/**
	 * @param mixed $criteria
	 * @param \Exception $previous
	 */
	public function __construct($criteria = null, Exception $previous = null) {
		if (is_array($criteria)) {
			$message = 'Product not found by criteria ' . Debug::export($criteria);
		} else {
			$message = $criteria;
		}
		parent::__construct($message, $previous, 0);
	}

}
