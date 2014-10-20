<?php

namespace SS6\ShopBundle\Model\Product\Parameter\Exception;

use Exception;
use SS6\ShopBundle\Model\Product\Parameter\Exception\ParameterException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ParameterNotFoundException extends NotFoundHttpException implements ParameterException {

	/**
	 * @param mixed $criteria
	 * @param \Exception $previous
	 */
	public function __construct($criteria, Exception $previous = null) {
		parent::__construct('Product parameter not found by criteria ' . var_export($criteria, true), $previous, 0);
	}

}
