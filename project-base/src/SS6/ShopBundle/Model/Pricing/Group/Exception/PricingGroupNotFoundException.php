<?php

namespace SS6\ShopBundle\Model\Pricing\Group\Exception;

use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PricingGroupNotFoundException extends NotFoundHttpException implements PricingGroupException {

	/**
	 * @param mixed $criteria
	 * @param \Exception $previous
	 */
	public function __construct($criteria, Exception $previous = null) {
		parent::__construct('Pricing group not found by criteria ' . var_export($criteria, true), $previous, 0);
	}
}
