<?php

namespace SS6\ShopBundle\Model\Administrator\Exception;

use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdministratorNotFoundException extends NotFoundHttpException implements AdministratorException {

	/**
	 * @param mixed $criteria
	 * @param \Exception $prevoious
	 */
	public function __construct($criteria, Exception $prevoious = null) {
		parent::__construct('Administrator not found by criteria '. var_export($criteria, true), $previous, 0);
	}
}
