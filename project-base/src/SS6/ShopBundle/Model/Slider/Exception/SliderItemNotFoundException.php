<?php

namespace SS6\ShopBundle\Model\Slider\Exception;

use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SliderItemNotFoundException extends NotFoundHttpException implements SliderItemException {

	/**
	 * @param mixed $criteria
	 * @param \Exception $previous
	 */
	public function __construct($criteria, Exception $previous = null) {
		parent::__construct('Slider item not found by criteria ' . var_export($criteria, true), $previous, 0);
	}

}