<?php

namespace SS6\ShopBundle\Model\Product\Availability\Exception;

use Exception;
use SS6\ShopBundle\Component\Debug;
use SS6\ShopBundle\Model\Product\Availability\Exception\AvailabilityException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AvailabilityNotFoundException extends NotFoundHttpException implements AvailabilityException {

	/**
	 * @param mixed $criteria
	 * @param \Exception $previous
	 */
	public function __construct($criteria, Exception $previous = null) {
		parent::__construct('Product availability not found by criteria ' . Debug::export($criteria), $previous, 0);
	}

}
