<?php

namespace SS6\ShopBundle\Model\Product\Flag\Exception;

use Exception;
use SS6\ShopBundle\Component\Debug;
use SS6\ShopBundle\Model\Product\Flag\Exception\FlagException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FlagNotFoundException extends NotFoundHttpException implements FlagException {

	/**
	 * @param mixed $criteria
	 * @param \Exception $previous
	 */
	public function __construct($criteria, Exception $previous = null) {
		parent::__construct('Product flag not found by criteria ' . Debug::export($criteria), $previous, 0);
	}

}
