<?php

namespace SS6\ShopBundle\Model\Department\Exception;

use Exception;
use SS6\ShopBundle\Component\Debug;
use SS6\ShopBundle\Model\Department\Exception\DepartmentException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DepartmentNotFoundException extends NotFoundHttpException implements DepartmentException {

	/**
	 * @param mixed $criteria
	 * @param \Exception $previous
	 */
	public function __construct($criteria, Exception $previous = null) {
		parent::__construct('Department not found by criteria ' . Debug::export($criteria), $previous, 0);
	}

}
