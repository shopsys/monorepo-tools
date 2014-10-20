<?php

namespace SS6\ShopBundle\Model\Customer\Exception;

use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserNotFoundException extends NotFoundHttpException implements CustomerException {

	/**
	 * @param mixed $criteria
	 * @param \Exception $previous
	 */
	public function __construct($criteria, Exception $previous = null) {
		parent::__construct('User not found by criteria ' . var_export($criteria, true), $previous, 0);
	}

}
