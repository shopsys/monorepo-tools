<?php

namespace SS6\ShopBundle\Model\Pricing\Group\Exception;

use Exception;
use SS6\ShopBundle\Component\Debug;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PricingGroupNotFoundException extends NotFoundHttpException implements PricingGroupException {

	/**
	 * @param mixed $criteria
	 * @param \Exception $previous
	 */
	public function __construct($criteria, Exception $previous = null) {
		parent::__construct('Pricing group not found by criteria ' . Debug::export($criteria), $previous, 0);
	}
}
