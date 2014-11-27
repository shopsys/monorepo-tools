<?php

namespace SS6\ShopBundle\Model\Slider\Exception;

use Exception;
use SS6\ShopBundle\Component\Debug;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SliderItemNotFoundException extends NotFoundHttpException implements SliderItemException {

	/**
	 * @param mixed $criteria
	 * @param \Exception $previous
	 */
	public function __construct($criteria, Exception $previous = null) {
		parent::__construct('Slider item not found by criteria ' . Debug::export($criteria), $previous, 0);
	}

}
