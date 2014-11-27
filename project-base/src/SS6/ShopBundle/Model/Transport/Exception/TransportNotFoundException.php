<?php

namespace SS6\ShopBundle\Model\Transport\Exception;

use Exception;
use SS6\ShopBundle\Component\Debug;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TransportNotFoundException extends NotFoundHttpException implements TransportException {

	/**
	 * @param mixed $criteria
	 * @param \Exception $previous
	 */
	public function __construct($criteria, Exception $previous = null) {
		parent::__construct('Transport not found by criteria ' . Debug::export($criteria), $previous, 0);
	}

}
